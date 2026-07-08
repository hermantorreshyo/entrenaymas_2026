<script type="text/template" id="web_seo_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <% var modulo = control.get("web_seo") %>
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuracion","en"=>"Configuration")); ?>
    <% if (ID_PROYECTO != 14) { %>
      / <b><%= modulo.title %></b>
    <% } %>
  </h1>
</div>
<div class="wrapper-md">
  <div class="centrado rform">
    <div class="row">

      <div class="col-md-10 col-md-offset-1">

        <% if (ID_EMPRESA == 1052) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Par&aacute;metros",
                      "en"=>"Parameters",
                    )); ?>
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
            <div class="panel-body expand" style="display:block">
              <div class="padder">
                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" id="toque_cobro_efectivo" name="mostrar_numeros_direccion_detalle" <%= (mostrar_numeros_direccion_detalle == 1) ? 'checked' : '' %>><i></i>
                      Habilitar portada unica.
                    </label>
                  </div>                    
                </div>
              </div>
            </div>
          </div>
        <% } %>        

        <% if (ID_PROYECTO != 14) { %>
          <div class="panel panel-default <%= (PERFIL == 862)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Optimizaci&oacute;n para buscadores",
                      "en"=>"Search Engine Optimization",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Herramientas para mejorar el posicionamiento de su sitio.",
                      "en"=>"You can improve the position of your web with this tools.",
                    )); ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Titulo","en"=>"Title")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Titulo del navegador cuando se visualice la pagina.","en"=>"Web page title")); ?>" name="seo_title" class="form-control"><%= seo_title %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Descripci&oacute;n","en"=>"Description")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Escribe una breve descripcion de la pagina.","en"=>"Short description about your company")); ?>" name="seo_description" class="form-control"><%= seo_description %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Palabras Clave","en"=>"Keywords")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Escribe las palabras clave que describan tu actividad.","en"=>"Comma-separated words that describe your bussiness")); ?>" name="seo_keywords" class="form-control"><%= seo_keywords %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Personalizar robots.txt","en"=>"Customize robots.txt")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Ingresa las lineas de tu archivo robots.txt.","en"=>"Write here the lines of your robots.txt file")); ?>" name="seo_robots" class="form-control"><%= seo_robots %></textarea>
                </div>
              </div>
            </div>
          </div>
        <% } %>

        <% if (IDIOMA != "en" && typeof DOMINIO != "undefined" && !isEmpty(DOMINIO) && ID_PROYECTO != 14 && MILLING == 0) { %>
          <div class="panel panel-default <%= (PERFIL == 862)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Integración con MercadoLibre",
                      "en"=>"Integración con MercadoLibre",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Habilite la sincronizacion del sistema con MercadoLibre.",
                      "en"=>"Habilite la sincronizacion del sistema con MercadoLibre.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <% if (isEmpty(ML_ACCESS_TOKEN)) { %>
                  <a class="btn btn-default" href="https://www.varcreative.com/connect_meli.php?id_empresa=<%= ID_EMPRESA %>" target="_blank">Habilitar la sincronizacion</a>
                <% } else { %>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Lista de precios</label>
                        <select class="form-control" name="ml_lista_base"> 
                          <option value="0" <%= (typeof ml_lista_base != "undefined" && ml_lista_base == 0 ? "selected":"") %>>Lista 1</option>
                          <option value="1" <%= (typeof ml_lista_base != "undefined" && ml_lista_base == 1 ? "selected":"") %>>Lista 2</option>
                          <option value="2" <%= (typeof ml_lista_base != "undefined" && ml_lista_base == 2 ? "selected":"") %>>Lista 3</option>
                          <option value="3" <%= (typeof ml_lista_base != "undefined" && ml_lista_base == 3 ? "selected":"") %>>Lista 4</option>
                          <option value="4" <%= (typeof ml_lista_base != "undefined" && ml_lista_base == 4 ? "selected":"") %>>Lista 5</option>
                          <option value="5" <%= (typeof ml_lista_base != "undefined" && ml_lista_base == 5 ? "selected":"") %>>Lista 6</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Recargo adicional a la lista seleccionada</label>
                        <input type="text" class="form-control" name="ml_recargo_precio" value="<%= ml_recargo_precio %>" />
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label">Descripcion de su empresa que se compartira con sus productos</label>
                    <textarea name="ml_texto_empresa" class="form-control h200"><%= ml_texto_empresa %></textarea>
                  </div>

                  <?php
                  $label = lang(array(
                    "es"=>"Im&aacute;genes para todos los productos",
                    "en"=>"Photos",
                  )); ?>
                  <?php 
                  multiple_upload(array(
                    "name"=>"images_meli",
                    "label"=>$label,
                    "url"=>"articulos/function/save_image/",
                    "width"=>(isset($empresa->config["producto_galeria_image_width"]) ? $empresa->config["producto_galeria_image_width"] : 800),
                    "height"=>(isset($empresa->config["producto_galeria_image_height"]) ? $empresa->config["producto_galeria_image_height"] : 600),
                    "resizable"=>(isset($empresa->config["producto_galeria_image_resizable"]) ? $empresa->config["producto_galeria_image_resizable"] : 0),
                    "upload_multiple"=>true,
                  )); ?>

                  <a class="btn btn-default borrar_sincro_meli">Dejar de sincronizar</a>
                  <% if (ID_PROYECTO == 1 || ID_PROYECTO == 2) { %>
                    <a class="btn btn-default sincronizacion_completa_articulos_meli">Resubir todo a MercadoLibre</a>
                    <a class="btn btn-default importacion_meli">Importar de MercadoLibre</a>
                  <% } %>

                <% } %>
              </div>
            </div>
          </div>

          <% if (ID_PROYECTO == 3) { %>
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="padder">
                  <div class="form-group mb0 clearfix">
                    <label class="control-label">
                      Integración con Argenprop
                    </label>
                    <a class="expand-link fr">
                      <?php echo lang(array(
                        "es"=>"+ Ver opciones",
                        "en"=>"+ View options",
                      )); ?>
                    </a>
                    <div class="panel-description">Configure la sincronización de propiedades con Argenprop.</div>
                  </div>
                </div>
              </div>
              <div class="panel-body expand">
                <div class="padder">
                  <div class="form-group">
                    <label class="control-label">Usuario</label>
                    <input type="text" class="form-control" name="argenprop_usuario" value="<%= argenprop_usuario %>" />
                  </div>
                  <div class="form-group">
                    <label class="control-label">Contraseña</label>
                    <input type="text" class="form-control" name="argenprop_password" value="<%= argenprop_password %>" />
                  </div>
                  <div class="form-group">
                    <label class="control-label">ID Vendedor</label>
                    <input type="text" class="form-control" name="argenprop_id_vendedor" value="<%= argenprop_id_vendedor %>" />
                  </div>
                </div>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-body">
                <div class="padder">
                  <div class="form-group mb0 clearfix">
                    <label class="control-label">
                      Integración con Tokko Brokers
                    </label>
                    <a class="expand-link fr">
                      <?php echo lang(array(
                        "es"=>"+ Ver opciones",
                        "en"=>"+ View options",
                      )); ?>
                    </a>
                    <div class="panel-description">Parámetros de configuración con Tokko Brokers.</div>
                  </div>
                </div>
              </div>
              <div class="panel-body expand">
                <div class="padder">
                  <div class="form-group">
                    <label class="control-label">API Key</label>
                    <input type="text" class="form-control" name="tokko_apikey" value="<%= tokko_apikey %>" />
                  </div>
                  <div class="form-group">
                    <div class="checkbox">
                      <label class="i-checks">
                        <input type="checkbox" id="web_seo_tokko_enviar_consultas" name="tokko_enviar_consultas" <%= (tokko_enviar_consultas == 1) ? 'checked' : '' %>><i></i> 
                        Enviar consultas a Tokko Brokers.
                      </label>
                    </div>                    
                  </div>                  
                  <div class="form-group">
                    <div class="checkbox">
                      <label class="i-checks">
                        <input type="checkbox" id="web_seo_tokko_importacion" name="tokko_importacion" <%= (tokko_importacion == 1) ? 'checked' : '' %>><i></i> 
                        Importar automáticamente propiedades de Tokko Brokers.
                      </label>
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
                      Integración con Inmobusqueda
                    </label>
                    <a class="expand-link fr">
                      <?php echo lang(array(
                        "es"=>"+ Ver opciones",
                        "en"=>"+ View options",
                      )); ?>
                    </a>
                    <div class="panel-description">Configure la sincronización de propiedades con Inmobusqueda.</div>
                  </div>
                </div>
              </div>
              <div class="panel-body expand">
                <div class="padder">
                  <div class="form-group">
                    <label class="control-label">URL de propiedades</label>
                    <input type="text" class="form-control" name="url_inmobusqueda" value="<%= url_inmobusqueda %>" />
                  </div>
                </div>
              </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-body">
                <div class="padder">
                  <div class="form-group mb0 clearfix">
                    <label class="control-label">
                      Red Inmovar
                    </label>
                    <a class="expand-link fr">
                      <?php echo lang(array(
                        "es"=>"+ Ver opciones",
                        "en"=>"+ View options",
                      )); ?>
                    </a>
                    <div class="panel-description">Parámetros de configuración para la Red Inmovar.</div>
                  </div>
                </div>
              </div>
              <div class="panel-body expand">
                <div class="padder">
                  <div class="form-group">
                    <div class="checkbox">
                      <label class="i-checks">
                        <input type="checkbox" id="web_seo_red_inmovar" name="red_inmovar" <%= (red_inmovar == 1) ? 'checked' : '' %>><i></i> 
                        Participar de la Red Inmovar
                      </label>
                    </div>                    
                  </div>
                </div>
              </div>
            </div>            
          <% } %>

          <div class="panel panel-default <%= (ID_PROYECTO == 3 || PERFIL == 862)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Tienda y carrito de compras",
                      "en"=>"Cart configuration",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Personalice su tienda para ajustar mejor la web a su negocio.",
                      "en"=>"Personalice su tienda para ajustar mejor la web a su negocio.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">

                <div class="form-group">
                  <label class="control-label">Carrito de compras</label>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="tienda_carrito" class="radio" value="0" <%= (tienda_carrito==0)?'checked=""':'' %>>
                      <i></i>
                      Activo con todo el proceso de pago
                    </label>
                  </div>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="tienda_carrito" class="radio" value="1" <%= (tienda_carrito==1)?'checked=""':'' %>>
                      <i></i>
                      Activo, pero solo envia un email con el pedido (no habilita el pago)
                    </label>
                  </div>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="tienda_carrito" class="radio" value="2" <%= (tienda_carrito==2)?'checked=""':'' %>>
                      <i></i>
                      Desactivo, no se puede comprar a traves de la web (el bot&oacute;n agregar al carrito desaparece)
                    </label>
                  </div>
                  <?php
                  // NOTA: TIENDA_CARRITO = 3 ES WHATSAPP
                  ?>
                </div>

                <div class="form-group">
                  <label class="control-label">Mostrar los precios de los art&iacute;culos</label>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="tienda_ver_precios" class="radio" value="0" <%= (tienda_ver_precios==0)?'checked=""':'' %>>
                      <i></i>
                      Todo el p&uacute;blico que visita la web puede ver los precios
                    </label>
                  </div>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="tienda_ver_precios" class="radio" value="1" <%= (tienda_ver_precios==1)?'checked=""':'' %>>
                      <i></i>
                      S&oacute;lo los usuarios registrados pueden ver los precios
                    </label>
                  </div>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="tienda_ver_precios" class="radio" value="2" <%= (tienda_ver_precios==2)?'checked=""':'' %>>
                      <i></i>
                      Los precios est&aacute;n ocultos en la web
                    </label>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label">Forma de agregar al carrito</label>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" class="tienda_comprar_listado" name="tienda_comprar_listado" class="radio" value="0" <%= (tienda_comprar_listado==0)?'checked=""':'' %>>
                      <i></i>
                      En el detalle del producto
                    </label>
                  </div>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" class="tienda_comprar_listado" name="tienda_comprar_listado" class="radio" value="1" <%= (tienda_comprar_listado==1)?'checked=""':'' %>>
                      <i></i>
                      Agrega directamente en el listado
                    </label>
                  </div>
                </div>                

                <div class="form-group">
                  <label class="control-label">Tipos de precios de la tienda</label>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="articulo_mostrar_precio_neto" class="radio" value="0" <%= (articulo_mostrar_precio_neto==0)?'checked=""':'' %>>
                      <i></i>
                      Precios finales (con IVA)
                    </label>
                  </div>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="articulo_mostrar_precio_neto" class="radio" value="1" <%= (articulo_mostrar_precio_neto==1)?'checked=""':'' %>>
                      <i></i>
                      Precios netos (sin IVA, en caso de pagar por la web se agrega al final)
                    </label>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Lista de precios para la web</label>
                      <select name="tienda_lista_precios" class="form-control">
                        <option <%= (tienda_lista_precios == 0)?"selected":"" %> value="0">Lista 1</option>
                        <option <%= (tienda_lista_precios == 2)?"selected":"" %> value="2">Lista 2</option>
                        <option <%= (tienda_lista_precios == 3)?"selected":"" %> value="3">Lista 3</option>
                        <option <%= (tienda_lista_precios == 4)?"selected":"" %> value="4">Lista 4</option>
                        <option <%= (tienda_lista_precios == 5)?"selected":"" %> value="5">Lista 5</option>
                        <option <%= (tienda_lista_precios == 6)?"selected":"" %> value="6">Lista 6</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="control-label">Convertir los precios de la web a moneda:</label>
                      <select class="form-control" name="tienda_moneda">
                        <option value="">No convertir (muestra la moneda de cada producto)</option>
                        <% for(var i=0;i< window.monedas.length;i++) { %>
                          <% var o = monedas[i]; %>
                          <option <%= (o.codigo == tienda_moneda)?"selected":"" %> value="<%= o.codigo %>">Convertir a: <%= o.codigo %></option>
                        <% } %>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label">Formulario de consulta de productos</label>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="tienda_consulta_productos" class="radio" value="0" <%= (tienda_consulta_productos==0)?'checked=""':'' %>>
                      <i></i>
                      Habilitar bot&oacute;n y formulario de consulta.
                    </label>
                  </div>
                  <div class="radio">
                    <label class="i-checks">
                      <input type="radio" name="tienda_consulta_productos" class="radio" value="1" <%= (tienda_consulta_productos==1)?'checked=""':'' %>>
                      <i></i>
                      Deshabilitar bot&oacute;n y formulario de consulta.
                    </label>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label">Campos solicitados en el registro de usuarios</label>
                  <div class="clearfix">
                    <div class="checkbox fl m-r mt0">
                      <label class="i-checks">
                        <input type="checkbox" id="web_seo_tienda_registro_telefono" name="tienda_registro_telefono" <%= (tienda_registro_telefono == 1) ? 'checked' : '' %>><i></i> Tel&eacute;fono
                      </label>
                    </div>
                    <div class="checkbox fl m-r mt0">
                      <label class="i-checks">
                        <input type="checkbox" id="web_seo_tienda_registro_direccion" name="tienda_registro_direccion" <%= (tienda_registro_direccion == 1) ? 'checked' : '' %>><i></i> Direccion
                      </label>
                    </div>
                    <div class="checkbox fl m-r mt0">
                      <label class="i-checks">
                        <input type="checkbox" id="web_seo_tienda_registro_documento" name="tienda_registro_documento" <%= (tienda_registro_documento == 1) ? 'checked' : '' %>><i></i> DNI/CUIT
                      </label>
                    </div>
                    <div class="checkbox fl m-r mt0">
                      <label class="i-checks">
                        <input type="checkbox" id="web_seo_tienda_registro_ciudad" name="tienda_registro_ciudad" <%= (tienda_registro_ciudad == 1) ? 'checked' : '' %>><i></i> Ciudad
                      </label>
                    </div>
                    <div class="checkbox fl m-r mt0">
                      <label class="i-checks">
                        <input type="checkbox" id="web_seo_tienda_registro_password" name="tienda_registro_password" <%= (tienda_registro_password == 1) ? 'checked' : '' %>><i></i> Password
                      </label>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label">Punto de Venta utilizado para la web</label>
                  <select class="form-control" name="id_punto_venta" id="web_seo_puntos_venta">
                    <% for(var i=0;i< puntos_venta.length;i++) { %>
                      <% var pv = puntos_venta[i] %>
                      <option <%= (id_punto_venta == pv.id) ? "selected":"" %> value="<%= pv.id %>"><%= pv.nombre %></option>
                    <% } %>
                  </select>
                </div>

                <div class="form-group">
                  <label class="control-label">Compra m&iacute;nima</label>
                  <input type="text" class="form-control" value="<%= tienda_compra_minima %>" name="tienda_compra_minima" id="web_seo_tienda_compra_minima"/>
                </div>

                <div class="form-group">
                  <label class="control-label">Habilitar env&iacute;o a domicilio a partir del siguiente valor del carrito</label>
                  <input type="text" class="form-control" value="<%= tienda_envio_desde %>" name="tienda_envio_desde" id="web_seo_tienda_envio_desde"/>
                </div>

                <div class="form-group">
                  <label class="control-label">Items en listado de productos</label>
                  <select name="entradas_por_pagina" class="form-control">
                    <option <%= (entradas_por_pagina == 8)?"selected":"" %> value="8">8</option>
                    <option <%= (entradas_por_pagina == 16)?"selected":"" %> value="16">16</option>
                    <option <%= (entradas_por_pagina == 32)?"selected":"" %> value="32">32</option>
                    <option <%= (entradas_por_pagina == 64)?"selected":"" %> value="64">64</option>
                    <option <%= (entradas_por_pagina == 999999)?"selected":"" %> value="999999">Todos</option>
                  </select>
                </div>

              </div>
            </div>
          </div>

          <div class="panel panel-default <%= (ID_PROYECTO == 3 || PERFIL == 862)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Listado de Productos",
                      "en"=>"List of Items",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Configure distintas opciones para mostrar sus productos.",
                      "en"=>"Diferent options to show the products.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Orden en el listado</label>
                  <select class="form-control" name="orden_listado" id="web_seo_orden_listado">
                    <option <%= (orden_listado == 0)?"selected":"" %> value="0">Destacados</option>
                    <option <%= (orden_listado == 1)?"selected":"" %> value="1">Precio de Menor a Mayor</option>
                    <option <%= (orden_listado == 2)?"selected":"" %> value="2">Precio de Mayor a Menor</option>
                    <option <%= (orden_listado == 3)?"selected":"" %> value="3">Orden Alfabetico</option>
                    <option <%= (orden_listado == 4)?"selected":"" %> value="4">Últimos cargados</option>
                    <option <%= (orden_listado == 5)?"selected":"" %> value="5">Aleatorio</option>
                  </select>
                </div>
              </div>
            </div>
          </div>                 

          <div class="panel panel-default <%= (ID_PROYECTO == 3 || PERFIL == 862)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Descuentos generales",
                      "en"=>"Descuentos generales",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Configure descuentos que se aplican segun la cantidad de productos o el total del carrito.",
                      "en"=>"Configure descuentos que se aplican segun la cantidad de productos o el total del carrito.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <p class="text-muted">A partir cierta cantidad de productos, o determinado monto total del carrito, se aplica un descuento global a la compra del cliente.</p>
                <div class="row">
                  <div class="col-md-4">
                    <label class="control-label">Cantidad de productos</label>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">Monto total</label>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">Porcentaje de descuento aplicado</label>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_cantidad_1 %>" name="tienda_descuento_cantidad_1"/>
                    </div>    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_monto_1 %>" name="tienda_descuento_monto_1"/>
                    </div>    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_porcentaje_1 %>" name="tienda_descuento_porcentaje_1"/>
                    </div>    
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_cantidad_2 %>" name="tienda_descuento_cantidad_2"/>
                    </div>    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_monto_2 %>" name="tienda_descuento_monto_2"/>
                    </div>    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_porcentaje_2 %>" name="tienda_descuento_porcentaje_2"/>
                    </div>    
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_cantidad_3 %>" name="tienda_descuento_cantidad_3"/>
                    </div>    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_monto_3 %>" name="tienda_descuento_monto_3"/>
                    </div>    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_porcentaje_3 %>" name="tienda_descuento_porcentaje_3"/>
                    </div>    
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_cantidad_4 %>" name="tienda_descuento_cantidad_4"/>
                    </div>    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_monto_4 %>" name="tienda_descuento_monto_4"/>
                    </div>    
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="text" class="form-control" value="<%= tienda_descuento_porcentaje_4 %>" name="tienda_descuento_porcentaje_4"/>
                    </div>    
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default <%= (PERFIL == 862)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Emails autom&aacute;ticos y notificaciones",
                      "en"=>"Emails autom&aacute;ticos y notificaciones",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Indique los diferentes envios automaticos que realiza el sistema.",
                      "en"=>"Indique los diferentes envios automaticos que realiza el sistema.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <% if (control.check("emails_templates") > 0 && ID_PROYECTO == 2) { %>

                  <p class="bold">Carrito abandonado:</p>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Plantilla para enviar</label>
                        <select class="w100p" name="id_email_carrito_abandonado" id="web_seo_email_carrito_abandonado"></select>
                      </div>                    
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Enviar luego de: (horas)</label>
                        <input type="text" class="form-control" value="<%= tiempo_envio_carrito_abandonado %>" name="tiempo_envio_carrito_abandonado"/>                  
                      </div>                    
                    </div>
                  </div>

                  <p class="bold">Luego de que el cliente finaliza una compra:</p>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Plantilla para enviar</label>
                        <select class="w100p" name="id_email_post_compra" id="web_seo_id_email_post_compra"></select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Condicion</label>
                        <select id="web_seo_email_post_compra_condicion" name="email_post_compra_condicion" class="form-control">
                          <option <%= (email_post_compra_condicion == 0)?"selected":"" %> value="0">Todas las compras</option>
                          <option <%= (email_post_compra_condicion == 1)?"selected":"" %> value="1">Aquellas que superen el siguiente valor total</option>
                          <option <%= (email_post_compra_condicion == 2)?"selected":"" %> value="2">Aquellas que superen la siguiente cantidad de productos</option>
                        </select>
                      </div>                    
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">Valor</label>
                        <input type="text" id="web_seo_email_post_compra_condicion_valor" value="<%= email_post_compra_condicion_valor %>" name="email_post_compra_condicion_valor" class="form-control" />
                      </div>
                    </div>
                  </div>

                <% } %>

                <p class="bold">Registro de Usuario</p>
                <div class="form-group">
                  <label class="control-label">Enviar la siguiente plantilla de email cuando se registra un nuevo usuario</label>
                  <select class="w100p" name="id_email_registro" id="web_seo_id_email_registro"></select>
                </div>

                <p class="bold">Notificaciones</p>
                <div class="form-group">
                  <label class="control-label">Enviar como copia oculta las notificaciones del sistema a las siguientes direcciones (separadas por coma)</label>
                  <input type="text" class="form-control" value="<%= bcc_email %>" name="bcc_email"/>
                </div>

                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" id="crm_notificar_tareas" name="crm_notificar_tareas" <%= (crm_notificar_tareas == 1) ? 'checked' : '' %>><i></i>
                      Notificar por email cuando se asigna una nueva tarea.
                    </label>
                  </div>                    
                </div>

                <div class="form-group">
                  <div class="checkbox">
                    <label class="i-checks">
                      <input type="checkbox" id="crm_notificar_asignaciones_usuarios" name="crm_notificar_asignaciones_usuarios" <%= (crm_notificar_asignaciones_usuarios == 1) ? 'checked' : '' %>><i></i>
                      Notificar por email cuando se asigna un usuario a un contacto.
                    </label>
                  </div>                    
                </div>

                <% if (ID_PROYECTO == 3) { %>
                  <div class="form-group">
                    <label class="control-label">Notificaciones de nuevas consultas</label>
                    <select id="crm_enviar_emails_usuarios" name="crm_enviar_emails_usuarios" class="form-control">
                      <option <%= (crm_enviar_emails_usuarios == 0)?"selected":"" %> value="0">Enviar email al correo de la empresa</option>
                      <option <%= (crm_enviar_emails_usuarios == 1)?"selected":"" %> value="1">Enviar email solo al usuario asignado de la propiedad</option>
                      <option <%= (crm_enviar_emails_usuarios == 2)?"selected":"" %> value="2">Enviar email a ambos correos, el de la empresa y el usuario asignado a la propiedad</option>
                    </select>
                  </div>                    
                <% } %>

              </div>
            </div>
          </div>
        <% } %>


        <div class="panel panel-default <%= (PERFIL == 862 || IDIOMA == "en")?"dn":"" %>">
          <div class="panel-body">
            <div class="padder">
              <div class="form-group mb0 clearfix">
                <label class="control-label">
                  <?php echo lang(array(
                    "es"=>"Monedas",
                    "en"=>"Currencies",
                  )); ?>
                </label>
                <a class="expand-link fr">
                  <?php echo lang(array(
                    "es"=>"+ Ver opciones",
                    "en"=>"+ View options",
                  )); ?>
                </a>
                <div class="panel-description">
                  <?php echo lang(array(
                    "es"=>"Modifique las cotizaciones de las diferentes monedas utilizadas en su sitio.",
                    "en"=>"Edit the currencies used on your site.",
                  )); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body expand">
            <div class="padder">
              <div class="form-group">
                <div class="radio">
                  <label class="i-checks">
                    <input type="radio" class="conversion_automatica" name="conversion_automatica" class="radio" value="1" <%= (conversion_automatica==1)?'checked=""':'' %>>
                    <i></i>
                    Utilizar cotizaci&oacute;n automatica (actualiza cada 1 hora segun cotizaci&oacute;n del Banco Naci&oacute;n).
                  </label>
                </div>
                <div class="radio">
                  <label class="i-checks">
                    <input type="radio" class="conversion_automatica" name="conversion_automatica" class="radio" value="0" <%= (conversion_automatica==0)?'checked=""':'' %>>
                    <i></i>
                    Utilizar mi propia cotizaci&oacute;n
                  </label>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label"><?php echo lang(array("es"=>"Dolar Estadounidense","en"=>"Dolar")); ?></label>
                    <input id="web_configuracion_dolar" type="text" class="form-control" name="dolar" <%= (conversion_automatica==1)?'disabled="disabled"':'' %> value="<%= dolar %>" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>



        <% if (ID_PROYECTO != 14) { %>
          <div class="panel panel-default <%= (PERFIL == 862)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"C&oacute;digos externos",
                      "en"=>"External codes",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Ingrese los bloques de c&oacute;digo de herramientas de proveedores externos, como por ej. Google Analytics.",
                      "en"=>"Enter here the code of external sources, for example Google Analytics.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Google Analytics</label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; el c&oacute;digo provisto por Google Analytics.","en"=>"Insert here the Google Analytics code.")); ?>" name="analytics" class="form-control"><%= analytics %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Google Tag Manager Head","en"=>"Google Tag Manager Head")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; el c&oacute;digo provisto por Google Tag Manager que iria en el <head>.","en"=>"Insert here the Head Google Tag Manager code.")); ?>" name="gtm_head" class="form-control"><%= gtm_head %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Google Tag Manager Body","en"=>"Google Tag Manager Body")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; el c&oacute;digo provisto por Google Tag Manager que iria en el <body>.","en"=>"Insert here the Body Google Tag Manager code.")); ?>" name="gtm_body" class="form-control"><%= gtm_body %></textarea>
                </div>

                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Verificacion de sitio de Google","en"=>"Google Site Verification")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; el c&oacute;digo provisto por Google.","en"=>"Insert here the Google Verification Site code.")); ?>" name="google_site_verification" class="form-control"><%= google_site_verification %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Remarketing de Google","en"=>"Google Remarketing Code")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; el c&oacute;digo de Remarketing provisto por Google.","en"=>"Insert here the Google Remarketing code.")); ?>" name="remarketing" class="form-control"><%= remarketing %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label">Google Adsense</label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; el c&oacute;digo provisto por Google Adsense.","en"=>"Insert here the Google Adwords code.")); ?>" name="adsense" class="form-control"><%= adsense %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label">View ID</label>
                  <textarea placeholder="<?php echo lang(array("es"=>"C&oacute;digo de View ID para enlazar estad&iacute;sticas de Google Analytics.","en"=>"Insert here the View ID Code to bind web statics.")); ?>" name="view_id" class="form-control"><%= view_id %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Pixel de Conversi&oacute;n de Facebook","en"=>"Facebook Pixel Conversion")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; el c&oacute;digo provisto por Facebook.","en"=>"Insert here the Facebook Pixel Conversion code.")); ?>" name="pixel_fb" class="form-control"><%= pixel_fb %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Chat flotante","en"=>"Floating chat")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; el c&oacute;digo provisto por su proveedor de chat.","en"=>"Insert here the chat code.")); ?>" name="zopim" class="form-control"><%= zopim %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label"><?php echo lang(array("es"=>"Codigo de Seguimiento de Contacto","en"=>"Contact Tracker Code")); ?></label>
                  <textarea placeholder="<?php echo lang(array("es"=>"Pegue aqu&iacute; los codigos que se ejecutarán luego de que el cliente envia un contacto.","en"=>"Insert here the code to track when the user submit a contact form.")); ?>" name="js_post_contacto" class="form-control"><%= js_post_contacto %></textarea>
                </div>
                <% if (MILLING == 0) { %>
                  <div class="form-group">
                    <label class="control-label">Link de AFIP</label>
                    <textarea placeholder="Pegue aqu&iacute; el c&oacute;digo QR generado desde la p&aacute;gina de la AFIP." name="codigo_afip" class="form-control"><%= codigo_afip %></textarea>
                  </div>
                <% } %>
              </div>
            </div>
          </div>
        <% } %>

        <% if (ID_PROYECTO == 14) { %>
          <?php /* CONFIGURACION DE CLIENAPP */ ?>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Configuraci&oacute;n General</label>

                  <?php include("configuracion_clienapp.php"); ?>
                  
                </div>
              </div>
            </div>
          </div>

        <% } %>

        <?php // SI TENEMOS WHATSAPP Y TAMBIEN TENEMOS UNA WEB PROPIA ?>
        <% if (ID_PROYECTO != 14 && control.check("estadisticas_whatsapp") > 0) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Configuracion de Whatsapp",
                      "en"=>"Whatsapp's Configuration",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Configure los parametros del chat flotante de Whatsapp.",
                      "en"=>"Set up the parameters Whatsapp's chat.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">

                <?php include("configuracion_clienapp.php"); ?>
                
              </div>
            </div>
          </div>
        <% } %>


        <% if (ID_EMPRESA == 263) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Par&aacute;metros",
                      "en"=>"External codes",
                    )); ?>
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
                  <label class="control-label">Valor de los anuncios</label>
                  <input type="text" class="form-control" value="<%= horarios_2 %>" name="horarios_2"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Cantidad de dias para el vencimiento</label>
                  <input type="text" class="form-control" value="<%= texto_quienes_somos %>" name="texto_quienes_somos"/>
                </div>
              </div>
            </div>
          </div>
        <% } else if (MILLING == 1) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Par&aacute;metros",
                      "en"=>"Parameters",
                    )); ?>
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
                  <label class="control-label">Online Magazine</label>
                  <textarea name="texto_quienes_somos" class="form-control h150"><%= texto_quienes_somos %></textarea>
                </div>
                <div class="form-group">
                  <label class="control-label">Factor (%)</label>
                  <input type="text" class="form-control" value="<%= estadisticas_factor %>" name="estadisticas_factor"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Enviar como copia oculta las notificaciones del sistema a las siguientes direcciones (separadas por coma)</label>
                  <input type="text" class="form-control" value="<%= bcc_email %>" name="bcc_email"/>
                </div>
              </div>
            </div>
          </div>
        <% } else if (TOQUE == 1) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Par&aacute;metros",
                      "en"=>"Parameters",
                    )); ?>
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
            <div class="panel-body expand" style="<%= (PERFIL == 862)?"display:block":"" %>">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Asignacion de pedidos</label>
                  <select name="mostrar_numeros_direccion_listado" class="form-control">
                    <option <%= (mostrar_numeros_direccion_listado == 0)?"selected":"" %> value="0">Asignacion Manual</option>
                    <option <%= (mostrar_numeros_direccion_listado == 1)?"selected":"" %> value="1">Asignacion Automatica</option>
                  </select>
                </div>        

                <div class="form-group">
                  <label class="control-label">Cartel Arriba</label>
                  <input type="text" name="tripadvisor" class="form-control" value="<%= tripadvisor %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Cartel Checkout</label>
                  <input type="text" name="pago_pending" class="form-control" value="<%= pago_pending %>"/>
                </div>


                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Limite de compra en efectivo</label>
                      <input type="text" name="toque_compra_maxima_efectivo" class="form-control" value="<%= toque_compra_maxima_efectivo %>"/>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">Limite de efectivo en primera compra</label>
                      <input type="text" name="toque_compra_maxima_efectivo_primer_cliente" class="form-control" value="<%= toque_compra_maxima_efectivo_primer_cliente %>"/>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Hora Inicio 1</label>
                      <input type="text" id="toque_hora_inicio_1" name="emails_alquileres" class="form-control" value="<%= emails_alquileres %>"/>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Hora Fin 1</label>
                      <input type="text" id="toque_hora_fin_1" name="emails_emprendimientos" class="form-control" value="<%= emails_emprendimientos %>"/>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Hora Inicio 2</label>
                      <input type="text" id="toque_hora_inicio_2" name="emails_tasaciones" class="form-control" value="<%= emails_tasaciones %>"/>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Hora Fin 2</label>
                      <input type="text" id="toque_hora_fin_2" name="emails_ventas" class="form-control" value="<%= emails_ventas %>"/>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Hora Inicio 3</label>
                      <input type="text" id="toque_hora_inicio_3" name="emails_contacto" class="form-control" value="<%= emails_contacto %>"/>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label class="control-label">Hora Fin 3</label>
                      <input type="text" id="toque_hora_fin_3" name="emails_registro" class="form-control" value="<%= emails_registro %>"/>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label">Valor de Envio Minimo Toque</label>
                  <input type="text" name="texto_quienes_somos" class="form-control" value="<%= texto_quienes_somos %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Distancia Minima de Envio (mts)</label>
                  <input type="text" name="texto_newsletter" class="form-control" value="<%= texto_newsletter %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Valor de Envio Variable ($/mt)</label>
                  <input type="text" name="texto_contacto" class="form-control" value="<%= texto_contacto %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Velocidad promedio repartidor (Metros por minuto)</label>
                  <input type="text" name="texto_staff" class="form-control" value="<%= texto_staff %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Coeficiente de Seguridad</label>
                  <input type="text" name="texto_registro_gracias" class="form-control" value="<%= texto_registro_gracias %>"/>
                </div>

                <div class="form-group">
                  <label class="control-label">Tiempo de Servicio (min)</label>
                  <input type="text" name="texto_registro" class="form-control" value="<%= texto_registro %>"/>
                </div>
                <div class="form-group">
                  <label class="control-label">Tiempo Promedio de Entrega (Si es CERO, se usa el tiempo de servicio manual) </label>
                  <input type="text" name="booking" class="form-control" value="<%= booking %>"/>
                </div>

                <% if (PERFIL == 660) { %>
                  <div class="form-group">
                    <div class="checkbox">
                      <label class="i-checks">
                        <input type="checkbox" id="toque_cobro_efectivo" name="mostrar_numeros_direccion_detalle" <%= (mostrar_numeros_direccion_detalle == 1) ? 'checked' : '' %>><i></i>
                        Habilitar pago en efectivo
                      </label>
                    </div>                    
                  </div>
                <% } %>

              </div>
            </div>
          </div>

        <% } else if (ID_EMPRESA == 259) { %>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Par&aacute;metros",
                      "en"=>"Parameters",
                    )); ?>
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
            <div class="panel-body expand" style="<%= (PERFIL == 862)?"display:block":"" %>">
              <div class="padder">
                <div class="form-group">
                  <label class="control-label">Conferecias</label>
                  <select name="emails_alquileres" class="form-control">
                    <option <%= (emails_alquileres == "")?"selected":"" %> value="">Habilitado</option>
                    <option <%= (emails_alquileres == "1")?"selected":"" %> value="1">Deshabilitado</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

        <% } %>

        <% if (MILLING == 0) { %>
          <div class="panel panel-default <%= (PERFIL == 862)?"dn":"" %>">
            <div class="panel-body">
              <div class="padder">
                <div class="form-group mb0 clearfix">
                  <label class="control-label">
                    <?php echo lang(array(
                      "es"=>"Configuracion Avanzada",
                      "en"=>"Advanced Configuration",
                    )); ?>
                  </label>
                  <a class="expand-link fr">
                    <?php echo lang(array(
                      "es"=>"+ Ver opciones",
                      "en"=>"+ View options",
                    )); ?>
                  </a>
                  <div class="panel-description">
                    <?php echo lang(array(
                      "es"=>"Edite contenido CSS y Javascript.",
                      "en"=>"You can edit CSS and Javascript content.",
                    )); ?>                  
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body expand">
              <div class="padder">
                <div class="tab-container">
                  <ul class="nav nav-tabs" role="tablist">
                    <li class="active">
                      <a href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-info"></i>CSS</a>
                    </li>
                    <li>
                      <a href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-info"></i>Javascript</a>
                    </li>
                  </ul>
                  <div class="tab-content">
                    <div id="tab1" class="tab-pane active panel-body">
                      <textarea class="form-control" style="height:400px" id="web_editor_texto_css" name="texto_css"><%= texto_css %></textarea>
                    </div>
                    <div id="tab2" class="tab-pane panel-body">
                      <textarea class="form-control" style="height:400px" id="web_editor_texto_js" name="texto_js"><%= texto_js %></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="line b-b m-b-lg"></div>
        <% } %>

      </div>
    </div>

    <div class="row">
      <div class="col-md-10 col-md-offset-1 tar">
        <button class="btn guardar btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
      </div>
    </div>
  </div>
</div>
</script>
