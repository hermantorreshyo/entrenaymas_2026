<script type="text/template" id="inicio_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col">
      <div class="bg-light lter b-b wrapper-md oh">
        <div class="col-xs-12">
          <div class="pull-left">
            <h1 class="m-n font-thin h3 text-black">Dashboard</h1>
            <small class="text-muted">Bienvenido al panel de control</small>
          </div>
          <div class="pull-right">
            <input type="text" id="dashboard_fecha_desde" value="" class="form-control w120 pull-left calendar">
            <input type="text" id="dashboard_fecha_hasta" value="" class="form-control w120 m-l-xs pull-left calendar">
            <button id="dashboard_buscar_button" class="btn btn-default m-l-xs pull-left"><i class="fa fa-search"></i></button>
          </div>
        </div>
      </div>
      <div class="wrapper-md">
        <div class="row text-center">
          <div class="col-xs-6 col-sm-3">
            <div class="panel padder-v item" style="height: 100px">
              <div id="dashboard_cantidad_facturas" class="h1 text-info font-thin h1"></div>
              <span class="text-muted text-md">Comprobantes Emitidos</span>
            </div>
          </div>
          <div class="col-xs-6 col-sm-3">
            <div class="block panel padder-v bg-success item" style="height: 100px">
              <span id="dashboard_total_ventas" class="text-white font-thin h1 block"></span>
              <span class="text-muted text-md">$ en Facturas de Ventas</span>
            </div>
          </div>
          <div class="col-xs-6 col-sm-3">
            <div class="block panel padder-v bg-info item" style="height: 100px">
              <span id="dashboard_cantidad_clientes" class="text-white font-thin h1 block"></span>
              <span class="text-muted text-md">Clientes</span>
            </div>
          </div>
          <div class="col-xs-6 col-sm-3">
            <div class="panel padder-v item" style="height: 100px">
              <div id="dashboard_cantidad_productos" class="font-thin h1"></div>
              <span class="text-muted text-md">Productos</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5">
            <div class="panel panel-default">
              <div class="panel-heading wrapper b-b b-light">
                <h4 class="m-t-none m-b-none text-muted">&Uacute;ltimos Comprobantes</h4>
              </div>
              <ul id="dashboard_ultimos_comprobantes" class="list-group list-group-lg m-b-none">
              </ul>
              <div class="panel-footer text-right">
                <a href="app/#ventas_listado" class="btn btn-default btn-sm">Ver todos</a>
              </div>
            </div>
          </div>
          <div class="col-md-7">
            <div class="panel panel-default">
              <div class="panel-heading wrapper b-b b-light">
                <h4 class="m-t-none m-b-none text-muted">Facturacion</h4>              
              </div>
              <div class="panel-body">
                <div id="facturacion_bar" style="height: 275px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col w-md bg-white-only b-l bg-auto no-border-xs">
      <div class="padder-md">      
        <div class="m-b text-md m-t">Actividad Reciente</div>
        <div class="streamline b-l m-b"></div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="shopvar_dashboard_template">
<div class="hbox hbox-auto-xs hbox-auto-sm">
  <div class="col-md-8 col-md-offset-2 dashboard">

    <% var total_pasos = 4 %>
    <% var paso = 0 %>
    <% paso = paso + ((isEmpty(LOGO_1)) ? 0 : 1) %>
    <% paso = paso + ((total_articulos == 0) ? 0 : 1) %>
    <% paso = paso + ((configurar_metodo_pago == 0) ? 0 : 1) %>
    <% paso = paso + ((configurar_forma_envio == 0) ? 0 : 1) %>

    <h3 class="subtitulo mt40"><b>¡Hola <%= NOMBRE %>!</b>
      <% if (paso < total_pasos) { %>
        Vamos a preparar juntos tu negocio en la nube
      <% } %>
    </h3>

    <% if (isEmpty(LOGO_1)) { %>
      <div id="sugerencia-configurar-web" class="panel sugerencia-nueva panel-default">
        <div class="panel-body">
          <div class="media">
            <span class="thumb-lg pull-left tac">
              <img src="/sistema/resources/images/shopvar-paso1.png"/>
            </span>
            <div class="media-body">
              <h4>Personaliz&aacute; tu sitio</h4>
              <p>Subí el logo de tu empresa y configurá los colores de tu sitio web.</p>
            </div>
          </div>                        
        </div>
      </div>
    <% } %> 
    <% if (total_articulos == 0) { %>
      <div id="sugerencia-primer-articulo" class="panel sugerencia-nueva panel-default">
        <div class="panel-body">
          <div class="media">
            <span class="thumb-lg pull-left tac">
              <img src="/sistema/resources/images/shopvar-paso2.png"/>
            </span>
            <div class="media-body">
              <h4>Public&aacute; tu primer artículo</h4>
              <p>Cargá el título, descripción e imágenes de tu primer producto.</p>
            </div>
          </div>
        </div>
      </div>
    <% } %>
    <% if (configurar_metodo_pago == 0) { %>
      <div id="sugerencia-forma-pago" class="panel sugerencia-nueva panel-default">
        <div class="panel-body">
          <div class="media">
            <span class="thumb-lg pull-left tac">
              <img src="/sistema/resources/images/shopvar-paso3.png"/>
            </span>
            <div class="media-body">
              <h4>Configur&aacute; tu m&eacute;todo de pago</h4>
              <p>Pod&eacute;s utilizar tu cuenta de MercadoPagos para recibir el dinero de las ventas en tu sitio web.</p>
            </div>
          </div>                        
        </div>
      </div>
    <% } %>
    <% if (configurar_forma_envio == 0) { %>
      <div id="sugerencia-metodo-envio" class="panel sugerencia-nueva panel-default">
        <div class="panel-body">
          <div class="media">
            <span class="thumb-lg pull-left tac">
              <img src="/sistema/resources/images/shopvar-paso4.png"/>
            </span>
            <div class="media-body">
              <h4>Configur&aacute; tu forma de env&iacute;o</h4>
              <p>Administr&aacute; todas las formas de env&iacute;o posibles que tu negocio ofrece.</p>
            </div>
          </div>                        
        </div>
      </div>
    <% } %>

    <% if (paso < total_pasos) { %>
      <h4 class="subtitulo mt20"><b>Completa las tareas de tu tienda online:</b> <%= paso %> de <%= total_pasos %></h4>
      <div class="progress mb0">
        <div class="progress-bar" role="progressbar" style="width:<%= Number(paso / total_pasos * 100).toFixed(5) %>%"></div>
      </div>
    <% } else { %>
      <div id="dashboard_shopvar_consultas"></div>
    <% } %>

    <h3 class="subtitulo mt40"><b>Necesitas ayuda?</b></h3>

    <div class="row">
      <div class="col-md-6">
        <div id="sugerencia-llamar-atencion-cliente" class="panel sugerencia-nueva panel-default">
          <div class="panel-body">
            <div class="media">
              <span class="thumb-lg pull-left tac mr0">
                <img src="/sistema/resources/images/ayuda-whatsapp.png"/>
              </span>
              <div class="media-body">
                <h4>Atención al cliente</h4>
                <p>Solicitá ayuda para configurar tu sitio y ponerlo en línea.</p>
                <a class="fs16 db mt10 bold mb15"><i class="fa fa-whatsapp text-success mr5 fs22 pr t3"></i>Llamar por Whatsapp</a>
                <button class="btn btn-sm btn-dark">SOLICITAR AYUDA</button>
              </div>
            </div>                        
          </div>
        </div>        
      </div>    
      <div class="col-md-6">
        <div id="sugerencia-llamar-soporte-tecnico" class="panel sugerencia-nueva panel-default">
          <div class="panel-body">
            <div class="media">
              <span class="thumb-lg pull-left tac mr0">
                <img src="/sistema/resources/images/ayuda-soporte.png"/>
              </span>
              <div class="media-body">
                <h4>Soporte técnico</h4>
                <p>Solicitá ayuda respecto a cuestiones técnicas o errores.</p>
                <a class="fs16 db mt10 bold mb15"><i class="fa fa-whatsapp text-success mr5 fs22 pr t3"></i>Llamar por Whatsapp</a>
                <button class="btn btn-sm btn-dark">SOLICITAR AYUDA</button>
              </div>
            </div>                        
          </div>
        </div>        
      </div>    
    </div>

  </div>
</div>

<?php /*
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col" style="padding:20px 10px;">
      <div class="col-md-9 col-xs-12">
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_pagos != 0 && sin_envios != 0 && datos_empresa != 0) { %>
        <div class="row" id="shopvar_dashboard_cajitas">
          <% if (turnos == 1) { %>
            <div class="col-xs-12 col-sm-4">
              <div class="panel padder padder-v dashboard_data_item">
                <div class="media">
                  <img class="thumb-lg pull-left" src="/sistema/resources/images/ventas.png"/>
                  <span class="texto">Cantidad Clientes</span>
                  <span class="numero" id="shopvar_dashboard_cantidad_clientes"><%= cantidad_clientes %></span>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-sm-4">
              <div class="panel padder padder-v dashboard_data_item" >
                <div class="media">
                  <img class="thumb-lg pull-left" src="/sistema/resources/images/dinero-recaudado.png"/>
                  <span class="texto">Cantidad Turnos</span>
                  <span class="numero" id="shopvar_dashboard_cantidad_turnos"><%= cantidad_turnos %></span>
                </div>
              </div>
            </div>
          <% } else { %>
            <div class="col-xs-12 col-sm-4">
              <div class="panel padder padder-v dashboard_data_item">
                <div class="media">
                  <img class="thumb-lg pull-left" src="/sistema/resources/images/ventas.png"/>
                  <span class="texto">Cantidad de Ventas</span>
                  <span class="numero" id="shopvar_dashboard_cantidad_ventas"><%= cantidad_ventas %></span>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-sm-4">
              <div class="panel padder padder-v dashboard_data_item" >
                <div class="media">
                  <img class="thumb-lg pull-left" src="/sistema/resources/images/dinero-recaudado.png"/>
                  <span class="texto">Dinero Recaudado</span>
                  <span class="numero" id="shopvar_dashboard_cantidad_ventas">$ <%= Number(total_ventas).toFixed(0) %></span>
                </div>
              </div>
            </div>
          <% } %>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/estadisticas.png"/>
                <span class="texto">Cantidad de Visitas</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas"><%= total_sesiones %></span>
              </div>
            </div>
          </div>
        </div>
        <% } %>
        <div class="">

          <% if (configurar_disenio == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_shopvar_configurar_disenio">
            <div class="panel-heading">Personaliz&aacute; la apariencia de tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/tienda.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sub&iacute; el logo de tu marca y configur&aacute; la apariencia de tu sitio web</div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 conf_disenio_si">Configurar el dise&ntilde;o de tu sitio web</a>
                    <a class="text-info m-l conf_disenio_no">No tengo inter&eacute;s de configurar el dise&ntilde;o</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (datos_empresa == 0) { %>
          <div class="panel sugerencia panel-default">
            <div class="panel-heading">Informaci&oacute;n de tu empresa</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/tu-empresa.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Complet&aacute; los datos de tu empresa: direcci&oacute;n, localidad, etc. </div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 conf_empresa_si">Cargar informaci&oacute;n</a>
                    <a class="text-info m-l conf_empresa_no">Lo har&eacute; m&aacute;s tarde</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (subir_elemento == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_shopvar_primer_elemento">
            <div class="panel-heading">Agreg&aacute; el primer producto a tu tienda</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/productos.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sube el primer producto a tu sitio web para empezar a vender</div>
                  <div class="mt10">
                    <a href="app/#articulo" class="btn btn-info btn16 subir_elemento_si">Agregar un nuevo producto</a>
                    <a class="text-info m-l subir_elemento_no">No tengo inter&eacute;s en subir un producto</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (sin_pagos == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_shopvar_conf_pagos">
            <div class="panel-heading">Agreg&aacute; un medio de pago a tu tienda</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/medio-de-pago.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Ofrec&eacute; MercadoPago o el medio de pago que mejor se adapte a tu negocio.</div>
                  <div class="mt10">
                    <a href="app/#medios_pago_configuracion" class="btn btn-info btn16 conf_pagos_si">Configurar medio de pago</a>
                    <a class="text-info m-l conf_pagos_no">No quiero recibir pagos online</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (sin_envios == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_shopvar_conf_envios">
            <div class="panel-heading">Configur&aacute; la forma de env&iacute;o</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/envio.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Configur&aacute; la forma de env&iacute;o que mejor se adapte a tu negocio</div>
                  <div class="mt10">
                    <a href="app/#articulo" class="btn btn-info btn16 conf_envio_si">Configurar forma de env&iacute;o</a>
                    <a class="text-info m-l conf_envio_no">No voy a enviar mis productos</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <div id="dashboard_shopvar_consultas"></div>

        </div>
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_pagos != 0 && sin_envios != 0) { %>
        <div class="row">
          <div class="col-sm-6 col-xs-12">
            <div class="form-group">
              <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
            </div>
          </div>
          <div class="col-sm-6 col-xs-12">
            <div class="form-group">
              <a href="app/#pedidos" class="btn btn18 btn-success btn-block">Ver todas las ventas</a>
            </div>
          </div>
        </div>
        <% } %>
      </div>

      <div class="col-md-3 col-xs-12">

        <% if (porcentaje < 100) { %>
        <div class="panel dashboard_mensaje padder padder-v">
          <div class="fs18 tac text-info">
            Porcentaje de la tienda
            <span class="fs22"><%= porcentaje %>%</span>
          </div>
          <p class="fs13 gris mt5">Personaliza tu sitio para completar tu tienda</p>
          <div class="progress-xs progress" value="<%= porcentaje %>" type="info">
            <div class="progress-bar progress-bar-info2" role="progressbar" style="width: <%= porcentaje %>%;"></div>
          </div>
        </div>
        <% } %>

        <div id="dashboard_ayuda"></div>

      </div>
    </div>
  </div>
*/ ?>
</script>

<script type="text/template" id="precios_template">
  <div ui-view="" class="fade-in-down ng-scope">
    <div class="bg-light lter b-b wrapper-md hidden-print ng-scope">
      <h1 class="m-n font-thin h3">Planes </h1>
    </div>
    <div class="wrapper-md ng-scope">
      <div class="row no-gutter m-t">

        <% for(var i=0; i< planes.length; i++) { %>
          <% var plan = planes[i] %>
          <div class="col-lg-3 col-md-4 col-sm-6">
            <% if (plan.id == ID_PLAN) { %>
              <div class="panel b-a m-t-n-md m-b-xl">
                <div class="wrapper bg-info text-center m-l-n-xxs m-r-n-xxs">
                  <h4 class="text-u-c m-b-none"><%= plan.nombre %></h4>
                  <h2 class="m-t-none">
                    <sup class="pos-rlt" style="top:-22px">$</sup>
                    <span class="text-2x text-lt"><%= Number(plan.precio_anual).toFixed(0) %></span>
                    <span class="text-xs">/ mes</span>
                  </h2>
                </div>
                <%= plan.observaciones %>
                <div class="panel-footer text-center b-t m-t bg-light lter">
                  <a href="javascript:void(0)" class="btn btn-info m">PLAN CONTRATADO</a>
                </div>
              </div>
            <% } else { %>
              <div class="panel b-a">
                <div class="panel-heading wrapper-xs bg-success no-border">          
                </div>
                <div class="wrapper text-center b-b b-light">
                  <h4 class="text-u-c m-b-none"><%= plan.nombre %></h4>
                  <h2 class="m-t-none">
                    <sup class="pos-rlt" style="top:-22px">$</sup>
                    <span class="text-2x text-lt"><%= Number(plan.precio_anual).toFixed(0) %></span>
                    <span class="text-xs">/ mes</span>
                  </h2>
                </div>
                <%= plan.observaciones %>
                <div class="panel-footer text-center">
                  <a data-id="<%= plan.id %>" class="contratar_plan btn btn-success m">CONTRATAR</a>
                </div>
              </div>
            <% } %>
          </div>
        <% } %>
        
      </div>
    </div>
  </div>  
</script>

<script type="text/template" id="classvar_dashboard_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col" style="padding:20px 10px;">
      <div class="col-md-9 col-xs-12">
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_envios != 0) { %>
        <div class="row" id="classvar_dashboard_cajitas">
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item">
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/cant-noticias.png"/>
                <span class="texto">Total de Entradas</span>
                <span class="numero" id="classvar_dashboard_total_entradas"><%= total_entradas %></span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/cant-usuarios.png"/>
                <span class="texto">Total de Consultas</span>
                <span class="numero" id="classvar_dashboard_total_consultas"><%= total_consultas %></span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/estadisticas.png"/>
                <span class="texto">Cantidad de Visitas</span>
                <span class="numero" id="classvar_dashboard_total_visitas"><%= total_sesiones %></span>
              </div>
            </div>
          </div>
        </div>
        <% } %>
        <div class="">

          <% if (configurar_disenio == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_classvar_configurar_disenio">
            <div class="panel-heading">Personaliz&aacute; la apariencia de tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/configurar-sitio.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sub&iacute; el logo de tu marca y configur&aacute; la apariencia de tu sitio web</div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 conf_disenio_si">Configurar el dise&ntilde;o de tu sitio web</a>
                    <a class="text-info m-l conf_disenio_no">No tengo inter&eacute;s de configurar el dise&ntilde;o</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (datos_empresa == 0) { %>
          <div class="panel sugerencia panel-default">
            <div class="panel-heading">Informaci&oacute;n de tu empresa</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/info-empresa.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Complet&aacute; los datos de tu empresa: direcci&oacute;n, localidad, etc. </div>
                  <div class="mt10">
                    <a href="javascript:void(0)" class="btn btn-info btn16 datos_empresa_si">Cargar informaci&oacute;n</a>
                    <a href="javascript:void(0)" class="text-info m-l datos_empresa_no">Lo har&eacute; m&aacute;s tarde</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (subir_elemento == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_classvar_primer_elemento">
            <div class="panel-heading">Agreg&aacute; la primera noticia a tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/entradas.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sube la primer noticia a tu sitio web</div>
                  <div class="mt10">
                    <a href="javascript:void(0)" class="btn btn-info btn16 subir_elemento_si">Agregar una nueva noticia</a>
                    <a href="javascript:void(0)" class="text-info m-l subir_elemento_no">No tengo inter&eacute;s en subir una noticia</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (sin_envios == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_classvar_conf_envios">
            <div class="panel-heading">Configur&aacute; las categorias de entradas</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/categorias.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Pod&eacute;s crear categorias y subcategorias para segmentar de la mejor manera la informaci&oacute;n de tu sitio.</div>
                  <div class="mt10">
                    <a href="app/#articulo" class="btn btn-info btn16 conf_envio_si">Configurar categorias</a>
                    <a class="text-info m-l conf_envio_no">Lo hare luego</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <div id="dashboard_classvar_consultas"></div>

        </div>
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_pagos != 0 && sin_envios != 0) { %>
        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
            </div>
          </div>
        </div>
        <% } %>
      </div>

      <div class="col-md-3 col-xs-12">

        <% if (porcentaje < 100) { %>
        <div class="panel dashboard_mensaje padder padder-v">
          <div class="fs18 tac text-info">
            Porcentaje de la tienda
            <span class="fs22"><%= porcentaje %>%</span>
          </div>
          <p class="fs13 gris mt5">Personaliza tu sitio para completar tu tienda</p>
          <div class="progress-xs progress" value="<%= porcentaje %>" type="info">
            <div class="progress-bar progress-bar-info2" role="progressbar" style="width: <%= porcentaje %>%;"></div>
          </div>
        </div>
        <% } %>

        <div id="dashboard_ayuda"></div>

      </div>
    </div>
  </div>
</script>

<script type="text/template" id="inforvar_dashboard_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col" style="padding:20px 10px;">
      <div class="col-md-9 col-xs-12">
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_envios != 0) { %>
        <div class="row" id="inforvar_dashboard_cajitas">
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item">
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/cant-noticias.png"/>
                <span class="texto">Total de Entradas</span>
                <span class="numero" id="inforvar_dashboard_total_entradas"><%= total_entradas %></span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/cant-usuarios.png"/>
                <span class="texto">Total de Consultas</span>
                <span class="numero" id="inforvar_dashboard_total_consultas"><%= total_consultas %></span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/estadisticas.png"/>
                <span class="texto">Cantidad de Visitas</span>
                <span class="numero" id="inforvar_dashboard_total_visitas"><%= total_sesiones %></span>
              </div>
            </div>
          </div>
        </div>
        <% } %>
        <div class="">

          <% if (configurar_disenio == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_inforvar_configurar_disenio">
            <div class="panel-heading">Personaliz&aacute; la apariencia de tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/configurar-sitio.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sub&iacute; el logo de tu marca y configur&aacute; la apariencia de tu sitio web</div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 conf_disenio_si">Configurar el dise&ntilde;o de tu sitio web</a>
                    <a class="text-info m-l conf_disenio_no">No tengo inter&eacute;s de configurar el dise&ntilde;o</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (datos_empresa == 0) { %>
          <div class="panel sugerencia panel-default">
            <div class="panel-heading">Informaci&oacute;n de tu empresa</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/info-empresa.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Complet&aacute; los datos de tu empresa: direcci&oacute;n, localidad, etc. </div>
                  <div class="mt10">
                    <a href="javascript:void(0)" class="btn btn-info btn16 datos_empresa_si">Cargar informaci&oacute;n</a>
                    <a href="javascript:void(0)" class="text-info m-l datos_empresa_no">Lo har&eacute; m&aacute;s tarde</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (subir_elemento == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_inforvar_primer_elemento">
            <div class="panel-heading">Agreg&aacute; la primera noticia a tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/entradas.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sube la primer noticia a tu sitio web</div>
                  <div class="mt10">
                    <a href="javascript:void(0)" class="btn btn-info btn16 subir_elemento_si">Agregar una nueva noticia</a>
                    <a href="javascript:void(0)" class="text-info m-l subir_elemento_no">No tengo inter&eacute;s en subir una noticia</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (sin_envios == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_inforvar_conf_envios">
            <div class="panel-heading">Configur&aacute; las categorias de entradas</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/categorias.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Pod&eacute;s crear categorias y subcategorias para segmentar de la mejor manera la informaci&oacute;n de tu sitio.</div>
                  <div class="mt10">
                    <a href="app/#articulo" class="btn btn-info btn16 conf_envio_si">Configurar categorias</a>
                    <a class="text-info m-l conf_envio_no">Lo hare luego</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <div id="dashboard_inforvar_consultas"></div>

        </div>
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_pagos != 0 && sin_envios != 0) { %>
        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
            </div>
          </div>
        </div>
        <% } %>
      </div>

      <div class="col-md-3 col-xs-12">

        <% if (porcentaje < 100) { %>
        <div class="panel dashboard_mensaje padder padder-v">
          <div class="fs18 tac text-info">
            Porcentaje de la tienda
            <span class="fs22"><%= porcentaje %>%</span>
          </div>
          <p class="fs13 gris mt5">Personaliza tu sitio para completar tu tienda</p>
          <div class="progress-xs progress" value="<%= porcentaje %>" type="info">
            <div class="progress-bar progress-bar-info2" role="progressbar" style="width: <%= porcentaje %>%;"></div>
          </div>
        </div>
        <% } %>

        <div id="dashboard_ayuda"></div>

      </div>
    </div>
  </div>
</script>

<script type="text/template" id="docvar_dashboard_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col" style="padding:20px 10px;">
      <div class="col-md-9 col-xs-12">
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_pagos != 0 && sin_envios != 0) { %>
        <div class="row" id="docvar_dashboard_cajitas">
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item">
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/cant-noticias.png"/>
                <span class="texto">Total de Entradas</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/cant-usuarios.png"/>
                <span class="texto">Total de Usuarios</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/estadisticas.png"/>
                <span class="texto">Cantidad de Visitas</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
        </div>
        <% } %>
        <div class="">

          <% if (configurar_disenio == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_docvar_configurar_disenio">
            <div class="panel-heading">Personaliz&aacute; la apariencia de tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/configurar-sitio.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sub&iacute; el logo de tu marca y configur&aacute; la apariencia de tu sitio web</div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 conf_disenio_si">Configurar el dise&ntilde;o de tu sitio web</a>
                    <a class="text-info m-l conf_disenio_no">No tengo inter&eacute;s de configurar el dise&ntilde;o</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (isEmpty(DIRECCION)) { %>
          <div class="panel sugerencia panel-default">
            <div class="panel-heading">Informaci&oacute;n de tu empresa</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/info-empresa.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Complet&aacute; los datos de tu empresa: direcci&oacute;n, localidad, etc. </div>
                  <div class="mt10">
                    <a href="app/#mis_datos" class="btn btn-info btn16">Cargar informaci&oacute;n</a>
                    <a class="text-info m-l">Lo har&eacute; m&aacute;s tarde</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (subir_elemento == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_docvar_primer_elemento">
            <div class="panel-heading">Agreg&aacute; la primera noticia a tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/entradas.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sube la primer noticia a tu sitio web</div>
                  <div class="mt10">
                    <a href="javascript:void(0)" class="btn btn-info btn16 subir_elemento_si">Agregar una nueva noticia</a>
                    <a href="javascript:void(0)" class="text-info m-l subir_elemento_no">No tengo inter&eacute;s en subir una noticia</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (sin_envios == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_docvar_conf_envios">
            <div class="panel-heading">Configur&aacute; las categorias de entradas</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/categorias.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Pod&eacute;s crear categorias y subcategorias para segmentar de la mejor manera la informaci&oacute;n de tu sitio.</div>
                  <div class="mt10">
                    <a href="app/#articulo" class="btn btn-info btn16 conf_envio_si">Configurar categorias</a>
                    <a class="text-info m-l conf_envio_no">Lo hare luego</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <div id="dashboard_docvar_consultas"></div>

        </div>
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_pagos != 0 && sin_envios != 0) { %>
        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
            </div>
          </div>
        </div>
        <% } %>
      </div>

      <div class="col-md-3 col-xs-12">

        <% if (porcentaje < 100) { %>
        <div class="panel dashboard_mensaje padder padder-v">
          <div class="fs18 tac text-info">
            Porcentaje de la tienda
            <span class="fs22"><%= porcentaje %>%</span>
          </div>
          <p class="fs13 gris mt5">Personaliza tu sitio para completar tu tienda</p>
          <div class="progress-xs progress" value="<%= porcentaje %>" type="info">
            <div class="progress-bar progress-bar-info2" role="progressbar" style="width: <%= porcentaje %>%;"></div>
          </div>
        </div>
        <% } %>

        <div id="dashboard_ayuda"></div>

      </div>
    </div>
  </div>
</script>


<script type="text/template" id="colvar_dashboard_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col" style="padding:20px 10px;">
      <div class="col-md-9 col-xs-12">
        <% if (configurar_disenio != 0 && subir_elemento) { %>
        <div class="row" id="docvar_dashboard_cajitas">
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item">
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/colvar-cant-noticias.png"/>
                <span class="texto">Total de Entradas</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/colvar-cant-usuarios.png"/>
                <span class="texto">Total de Alumnos</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/colvar-estadisticas.png"/>
                <span class="texto">Cantidad de Visitas</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
        </div>
        <% } %>
        <div class="">

          <% if (configurar_disenio == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_colvar_configurar_disenio">
            <div class="panel-heading">Personaliz&aacute; la apariencia de tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/configurar-sitio.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sub&iacute; el logo de tu marca y configur&aacute; la apariencia de tu sitio web</div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 conf_disenio_si">Configurar el dise&ntilde;o de tu sitio web</a>
                    <a class="text-info m-l conf_disenio_no">No tengo inter&eacute;s de configurar el dise&ntilde;o</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <% if (subir_elemento == 0) { %>
          <div class="panel sugerencia panel-default" id="dashboard_colvar_primer_elemento">
            <div class="panel-heading">Agreg&aacute; la primera noticia a tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/entradas.png"/>
                </span>
                <div class="media-body">
                  <div class="mt10">Sube la primer noticia a tu sitio web</div>
                  <div class="mt10">
                    <a href="javascript:void(0)" class="btn btn-info btn16 subir_elemento_si">Agregar una nueva noticia</a>
                    <a href="javascript:void(0)" class="text-info m-l subir_elemento_no">No tengo inter&eacute;s en subir una noticia</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <% } %>

          <div id="dashboard_colvar_consultas"></div>

        </div>
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_pagos != 0 && sin_envios != 0) { %>
        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
            </div>
          </div>
        </div>
        <% } %>
      </div>

      <div class="col-md-3 col-xs-12">

        <% if (porcentaje < 100) { %>
        <div class="panel dashboard_mensaje padder padder-v">
          <div class="fs18 tac text-info">
            Porcentaje de la tienda
            <span class="fs22"><%= porcentaje %>%</span>
          </div>
          <p class="fs13 gris mt5">Personaliza tu sitio para completar tu tienda</p>
          <div class="progress-xs progress" value="<%= porcentaje %>" type="info">
            <div class="progress-bar progress-bar-info2" role="progressbar" style="width: <%= porcentaje %>%;"></div>
          </div>
        </div>
        <% } %>

        <div id="dashboard_ayuda"></div>

      </div>
    </div>
  </div>
</script>


<script type="text/template" id="clienapp_dashboard_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col" style="padding:20px 10px;">
      <div class="col-md-9 col-xs-12">
        <% if (configurar_disenio != 0 && subir_elemento) { %>
        <div class="row" id="docvar_dashboard_cajitas">
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item">
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/colvar-cant-noticias.png"/>
                <span class="texto">Total de Entradas</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/colvar-cant-usuarios.png"/>
                <span class="texto">Total de Alumnos</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/colvar-estadisticas.png"/>
                <span class="texto">Cantidad de Visitas</span>
                <span class="numero" id="shopvar_dashboard_cantidad_ventas">0</span>
              </div>
            </div>
          </div>
        </div>
        <% } %>
        <div class="">
          <div class="panel sugerencia panel-default">
            <div class="panel-heading">Agreg&aacute; el c&oacute;digo en tu web</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/entradas.png"/>
                </span>
                <div class="media-body pr10">
                  <div>Para terminar de configurar ClienApp en tu p&aacute;gina web, copi&aacute; el siguiente c&oacute;digo y pegalo antes de la etiqueta <b>&lt;/head></b>. Si ten&eacute;s alguna duda o problema para agregar el c&oacute;digo, no dudes en contactarte con nosotros, estamos para ayudarte!</div>
                  <div class="mt10">
                    <div class="show-code">&lt;script type="text/javascript" src="https://www.varcreative.com/sistema/resources/js/loader.js">&lt;/script><br/>
&lt;script type="text/javascript">loadScript("https://www.varcreative.com/sistema/whatsapp/get/"+window.location.hostname+"/");&lt;/script>
                    </div>
                  </div>
                  <div class="mt10">
                    <a class="btn btn-info btn16 copy-to-clipboard">Copiar c&oacute;digo</a>
                    <?php /*
                    <a href="javascript:void(0)" class="text-info m-l comprobar_codigo">Comprobar si el c&oacute;digo fue pegado correctamente en la web</a>
                    */ ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="panel sugerencia panel-default">
            <div class="panel-heading">Configur&aacute; el dominio de tu sitio</div>
            <div class="panel-body">
              <div class="media">
                <span class="thumb-lg pull-left">
                  <img class="img-circle" src="/sistema/resources/images/configurar-sitio.png"/>
                </span>
                <div class="media-body mr10">
                  <div>Ingres&aacute; la direcci&oacute;n de tu sitio web para terminar de configurar el chat:</div>
                  <div class="form-group mt10">
                    <input class="form-control" id="clienapp_dominio" type="form-control" value="<%= (typeof DOMINIO != 'undefined') ? DOMINIO.replace('/','') : '' %>" placeholder="Tu p&aacute;gina web...">
                  </div>
                  <div class="mt10">
                    <a class="btn btn-success btn16 guardar_dominio">Guardar cambios</a>
                  </div>
                </div>
              </div>                        
            </div>
          </div>
          <div id="dashboard_colvar_consultas"></div>

        </div>
        <% if (configurar_disenio != 0 && subir_elemento != 0 && sin_pagos != 0 && sin_envios != 0) { %>
        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
            </div>
          </div>
        </div>
        <% } %>
      </div>
      <div class="col-md-3 col-xs-12">
        <div id="dashboard_ayuda"></div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="tripvar_dashboard_template">
  <div class="hbox hbox-auto-xs hbox-auto-sm">
    <div class="col" style="padding:20px 10px;">
      <div class="col-md-9 col-xs-12">
        <div class="row" id="tripvar_dashboard_cajitas">
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item">
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/ventas.png"/>
                <span class="texto">Cantidad de Ventas</span>
                <span class="numero" id="tripvar_dashboard_cantidad_ventas"><%= cantidad_ventas %></span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/dinero-recaudado.png"/>
                <span class="texto">Dinero Recaudado</span>
                <span class="numero" id="tripvar_dashboard_cantidad_ventas">$ <%= Number(total_ventas).toFixed(0) %></span>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4">
            <div class="panel padder padder-v dashboard_data_item" >
              <div class="media">
                <img class="thumb-lg pull-left" src="/sistema/resources/images/estadisticas.png"/>
                <span class="texto">Cantidad de Visitas</span>
                <span class="numero" id="tripvar_dashboard_cantidad_ventas"><%= total_sesiones %></span>
              </div>
            </div>
          </div>
        </div>

        <div class="">
          <div id="dashboard_tripvar_consultas"></div>
        </div>
        <div class="row">
          <div class="col-sm-6 col-xs-12">
            <div class="form-group">
              <a href="app/#contactos" class="btn btn18 btn-info btn-block">Ver todas las consultas</a>
            </div>
          </div>
          <div class="col-sm-6 col-xs-12">
            <div class="form-group">
              <a href="app/#reservas" class="btn btn18 btn-success btn-block">Ver todas las ventas</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-xs-12">

        <div id="dashboard_ayuda"></div>

      </div>
    </div>
  </div>
</script>

<script type="text/template" id="notificacion_item_template">
  <a href="<%= link %>" class="media-body block m-b-none link">
    <%= texto %>
    <% if (!isEmpty(texto_2)) { %>
      <br/><small class="text-muted"><%= texto_2 %></small>
    <% } %>
  </a>
</script>


<script type="text/template" id="toque_dashboard_template">
  <div id="toque_dashboard_container" class="col">

    <div class="bg-light titulo-pagina lter b-b wrapper-md no-print">
      <div class="row">
        <div class="col-xs-12 col-sm-6">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-dashboard icono_principal"></i><b>Dashboard</b>
          </h1>
        </div>
        <div class="col-xs-12 col-sm-6">
          <div class="pull-right">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="toque_dashboard_fecha_desde" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="toque_dashboard_fecha_hasta" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="wrapper-md">
      
      <div class="row pagina">
        <div class="col-md-5">
          <div class="row row-sm text-center">
            <div class="col-xs-6">
              <div class="panel padder-v item bg-info" style="height: 140px">
                <div id="toque_dashboard_total_ventas" class="h2 font-thin text-white m-t-md">$ 0.00</div>
                <span class="text-muted text-md pt10 db">Total de ventas</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item bg-success" style="height: 140px">
                <div id="toque_dashboard_cantidad_operaciones" class="h2 font-thin text-white m-t-md">0</div>
                <span class="text-muted text-md pt10 db">Cantidad de operaciones</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item" style="height: 140px">
                <span id="toque_dashboard_ticket_promedio" class="font-thin h2 block m-t-md">$ 0.00</span>
                <span class="text-muted text-md pt10 db">Ticket promedio</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel padder-v item" style="height: 140px">
                <div id="toque_dashboard_venta_promedio" class="font-thin h2 m-t-md">$ 0.00</div>
                <span class="text-muted text-md pt10 db">Venta promedio por dia</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7 page-break">
          <div class="panel wrapper">
            <h4 class="font-thin m-t-none m-b text-muted">Env&iacute;os por d&iacute;a</h4>
            <div id="toque_dashboard_graficos" style="height: 235px;"></div>
          </div>
        </div>
      </div>
    
      <div class="pagina row">
        <div class="col-xs-12 col-md-4">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Top Comercios</div>
            <table class="toque_dashboard_table table-small table table-striped m-b-none">
              <tbody>
                <tr>
                  <td>Comercio 1</td>
                  <td class="tar">$ 0.00</td>
                </tr>
                <tr>
                  <td>Comercio 2</td>
                  <td class="tar">$ 0.00</td>
                </tr>
                <tr>
                  <td>Comercio 3</td>
                  <td class="tar">$ 0.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-xs-12 col-md-4">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Top Repartidores</div>
            <table class="toque_dashboard_table table-small table table-striped m-b-none">
              <tbody>
                <tr>
                  <td>Repartidor 1</td>
                  <td class="tar">$ 0.00</td>
                </tr>
                <tr>
                  <td>Repartidor 2</td>
                  <td class="tar">$ 0.00</td>
                </tr>
                <tr>
                  <td>Repartidor 3</td>
                  <td class="tar">$ 0.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-xs-12 col-md-4">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Top Clientes</div>
            <table class="toque_dashboard_table table-small table table-striped m-b-none">
              <tbody>
                <tr>
                  <td>Cliente 1</td>
                  <td class="tar">$ 0.00</td>
                </tr>
                <tr>
                  <td>Cliente 2</td>
                  <td class="tar">$ 0.00</td>
                </tr>
                <tr>
                  <td>Cliente 3</td>
                  <td class="tar">$ 0.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </div>
  </div>
</script>


<script type="text/template" id="toque_dashboard_comercio_template">
  <div id="toque_dashboard_container" class="col">

    <div class="bg-light titulo-pagina lter b-b wrapper-md no-print">
      <div class="row">
        <div class="col-xs-12 col-sm-6">
          <h1 class="m-n font-thin h3 text-black">
            <i class="fa fa-dashboard icono_principal"></i><b>Dashboard</b>
          </h1>
        </div>
        <div class="col-xs-12 col-sm-6">
          <div class="pull-right">
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="toque_dashboard_comercio_fecha_desde" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
            <div class="input-group pull-left" style="width: 140px;">
              <input type="text" id="toque_dashboard_comercio_fecha_hasta" class="form-control">
              <span class="input-group-btn">
                <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>              
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="wrapper-md">

      <div class="text-muted pb10 fs14">NOTA: Estos datos aquí mostrados son estimados a fines estadísticos, los valores reales finales son los que aparecen en su liquidación</div>
      
      <div class="row pagina">
        <div class="col-md-5">
          <div class="row row-sm text-center">
            <div class="col-xs-6">
              <div class="panel padder-v item bg-info" style="height: 140px">
                <div id="toque_dashboard_comercio_total_ventas" class="h2 font-thin text-white m-t-md">$ 0.00</div>
                <span class="text-muted text-md pt10 db">Total de ventas</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item bg-success" style="height: 140px">
                <div id="toque_dashboard_comercio_cantidad_operaciones" class="h2 font-thin text-white m-t-md">0</div>
                <span class="text-muted text-md pt10 db">Cantidad de operaciones</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="block panel padder-v item" style="height: 140px">
                <span id="toque_dashboard_comercio_ticket_promedio" class="font-thin h2 block m-t-md">$ 0.00</span>
                <span class="text-muted text-md pt10 db">Ticket promedio</span>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel padder-v item" style="height: 140px">
                <div id="toque_dashboard_comercio_venta_promedio" class="font-thin h2 m-t-md">$ 0.00</div>
                <span class="text-muted text-md pt10 db">Venta promedio por dia</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7 page-break">
          <div class="panel wrapper">
            <h4 class="font-thin m-t-none m-b text-muted">Env&iacute;os por d&iacute;a</h4>
            <div id="toque_dashboard_comercio_graficos" style="height: 235px;"></div>
          </div>
        </div>
      </div>
    
      <div class="pagina row">
        <div class="col-xs-12">
          <div class="panel panel-default" style="min-height:395px">
            <div class="panel-heading font-bold">Productos m&aacute;s vendidos</div>
            <table class="table-small table table-striped m-b-none">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody id="toque_dashboard_comercios_table"></tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</script>