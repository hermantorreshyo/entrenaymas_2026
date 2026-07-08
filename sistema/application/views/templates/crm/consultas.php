<script type="text/template" id="consultas_panel_template">
  <div>

    <% var modulo = control.get("consultas") %>
    <div class="bg-light lter b-b wrapper-md ng-scope clearfix">
      <h1 class="m-n font-thin h3 pull-left"><i class="<%= modulo.clase %> icono_principal"></i><%= modulo.nombre_es %></h1>
      <% if (ID_EMPRESA != 1284 && modulo.permiso >= 3) { %>
        <div class="btn-group dropdown pull-right">
          <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i></button>
          <ul class="dropdown-menu pull-right">
            <li><a href="app/#consultas_tipos">Estados</a></li>
          </ul>
        </div>
      <% } %>
    </div>    

    <div class="wrapper-md">
      <div class="row" style="margin:0px -5px">
        <div class="fl" style="width:<%= (100/(consultas_tipos.length + 1)) %>%">
          <div class="p5">
            <a href="javascript:void(0)" class="cambiar_tab btn-tab-large active btn-tab-azul" data-tipo="-1" data-custom_3="1">
              <b>Todos</b>
              <span class="consultas_estado consultas_estado_-1">(0)</span>
            </a>
          </div>
        </div>
        <% for(var i=0;i< consultas_tipos.length;i++) { %>
          <% var tipo = consultas_tipos[i] %>
          <div class="fl" style="width:<%= (100/(consultas_tipos.length + 1)) %>%">
            <div class="p5">
              <a href="javascript:void(0)" class="cambiar_tab btn-tab-large btn-tab-<%= tipo.color %>" data-tipo="<%= tipo.id %>" data-custom_3="1">
                <b><%= tipo.nombre %></b>
                <span class="consultas_estado consultas_estado_<%= tipo.id %>">(0)</span>
              </a>
            </div>  
          </div>
        <% } %>
      </div>

      <div class="mb20 mt15">
        <div class="clearfix">
          <div class="row">
            <div class="col-md-8 sm-m-b">
              <div class="input-group">
                <input type="text" id="consultas_buscar" value="<%= window.consultas_filter %>" placeholder="<?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?>..." autocomplete="off" class="form-control">
                <span class="input-group-btn">
                  <button class="btn btn-default buscar"><i class="fa fa-search"></i></button>
                </span>
                <span class="input-group-btn">
                  <button class="btn btn-default advanced-search-btn btn-addon btn-addon-2 ml5"><i class="fa fa-filter"></i><span><?php echo lang(array("es"=>"Filtros","en"=>"Filters")); ?></span></button>
                </span>

                  <% if (permiso == 3 && MILLING == 0) { %>
                    <?php /*
                    <span class="input-group-btn">
                      <div class="btn-group dropdown ml5">
                        <button class="btn btn-default btn-addon btn-addon-2 exportar_excel">
                          <i class="fa fa-upload"></i><span><?php echo lang(array("es"=>"Exportar","en"=>"Export")); ?></span>
                        </button>
                      </div>
                    </span>
                    */ ?>
                  <% } %>

              </div>
            </div>
            <div class="col-md-4 text-right">
              <% if (permiso > 1 && MILLING == 0) { %>
                <a class="btn btn-info nuevo_cliente btn-addon ml5" href="javascript:void(0)">
                  <i class="fa fa-plus"></i><span>&nbsp;&nbsp;<?php echo lang(array("es"=>"Nueva Consulta","en"=>"New Contact")); ?>&nbsp;&nbsp;</span>
                </a>
              <% } %>
            </div>
          </div>
        </div>
        <div class="advanced-search-div mt15 bg-light dk" style="<%= (window.consultas_codigo_propiedad != 0) ? "display:block" : "display:none" %>">
          <div class="wrapper pb0 oh">
            <div class="row pl10 pr10">

              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="input-group">
                    <input autocomplete="off" type="text" placeholder="Desde" id="consultas_desde" class="form-control">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>              
                  </div>
                </div>
              </div>
              <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                <div class="form-group">
                  <div class="input-group">
                    <input autocomplete="off" type="text" placeholder="Hasta" id="consultas_hasta" class="form-control">
                    <span class="input-group-btn">
                      <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>              
                  </div>
                </div>
              </div>

              <% if (ID_PROYECTO == 3) { %>
                <div class="col-md-2 col-sm-3 col-xs-12 h50 pr5 pl5">
                  <input type="text" placeholder="Código Propiedad" value="<%= window.consultas_codigo_propiedad %>" class="input form-control no-model" id="consultas_codigo_propiedad"/>
                </div>
              <% } %>
              <div class="col-md-1 col-sm-3 col-xs-12 h50 pr5 pl5">
                <button class="btn btn-default btn-dark btn-block buscar"><i class="fa fa-search m-r-xs"></i> <?php echo lang(array("es"=>"Buscar","en"=>"Search")); ?></button>
              </div>
            </div>
          </div>
        </div>
        
      </div>

      <div class="bulk_action wrapper pl0 pt0">
        <button class="btn btn-default eliminar_lote btn-addon"><i class="icon fa fa-trash"></i>Eliminar</button>
      </div>

      <div class="panel panel-default">
        <div class="table-responsive">
          <table id="consultas_table" class="table table-striped tabla-2 sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;"></th>
                <th class="w50 tac hidden-xs"></th>
                <th class="sorting" data-sort-by="nombre"><?php echo lang(array("es"=>"Nombre","en"=>"Name")); ?></th>
                <th class="col-xxs-0">Datos</th>
                <th></th>
                <th class="w25"></th>
                <th class="col-xxs-0"><span class="fl mr5">Origen</span> <i class="fa fa-filter ml5 mt5 fl"></i></th>
                <th class="col-xxs-0 sorting" data-sort-by="C.fecha_ult_operacion"><?php echo lang(array("es"=>"Fecha","en"=>"Date")); ?></th>
                <th class="w140"><?php echo lang(array("es"=>"Progreso","en"=>"Progress")); ?></th>
                <th class="w150"><?php echo lang(array("es"=>"Estado","en"=>"Status")); ?></th>
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

<script type="text/template" id="consultas_item">
  <% var clase = (activo==1)?"":"text-muted"; %>
  <td class="pr0">
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="<%= clase %> data hidden-xs pr0">
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
  <td class='data'>
    <div>
      <% if (isEmpty(nombre)) { %>
        <span class="capitalize text-link fs16"><%= email %></span>
      <% } else { %>
        <span class="capitalize text-link fs16"><%= nombre.ucwords() %></span>
      <% } %>
      <% if (ID_PROYECTO != 1) { %>
        <% if (!isEmpty(codigo)) { %><br/><span>Cod: <%= codigo %></span><% } %>
      <% } %>
    </div>

    <% if (!isEmpty(canal)) { %>
      <span class="label bg-info pull-left m-t-xs m-r-xs"><%= canal %></span>
    <% } %>

    <% if (etiquetas.length > 0) { %>
      <div class="clearfix">
        <% if (etiquetas.length > 0) { %>
          <% for(var j=0;j< etiquetas.length; j++) { %>
            <% var etiq = etiquetas[j] %>
            <span class="label bg-info pull-left m-t-xs m-r-xs"><%= etiq.nombre %></span>
          <% } %>
        <% } %>
        <% if (!isEmpty(observaciones)) { %>
          <i data-toggle="tooltip" title="<%= observaciones %>" class="fa fa-comments pull-left m-l-xs text-default"></i>
        <% } %>
      </div>
    <% } %>
    <div class="calification-container pull-left"></div>
  </td>
  <td class="col-xxs-0 <%= clase %>">
    <% if (!isEmpty(email)) { %>
      <i class="fa fa-envelope-o mr5"></i> 
      <% if (blur == 1) { %>
        <span class="cp text-link" style="color: transparent;text-shadow: 0 0 5px rgba(0,0,0,0.5);">**********************</span>
      <% } else { %>
        <span class="cp text-link"><%= email.toLowerCase() %></span>
      <% } %>
    <% } %>
    <% if (!isEmpty(telefono)) { %>

      <% if (blur == 1) { %>
        <br/><a class="cp" href="javascript:void(0)"><i class="fa fa-whatsapp text-success mr5"></i> <span class="text-link" style="color: transparent;text-shadow: 0 0 5px rgba(0,0,0,0.5);">+<%= fax %> **********</span></a>
      <% } else { %>
        <br/><a class="enviar_whatsapp cp" href="javascript:void(0)"><i class="fa fa-whatsapp text-success mr5"></i> <span class="text-link">+<%= fax %> <%= telefono %></span></a>
      <% } %>

    <% } %>
  </td>
  <td class="w200">
    <% if (blur == 2) { %>
      Esta consulta se encuentra oculta para el profesional.
      <a href="javascript:void(0)" class="mostrar_consulta text-warning dib mt10 tac">Mostrar consulta</a>
    <% } %>
  </td>
  <td class="tac">
    <% if ((typeof tarea_asignada != undefined) && tarea_asignada == 1) { %>
      <a href="app/#tareas">
        <i data-toggle="tooltip" title='<%= (typeof tarea_titulo != undefined) ? tarea_titulo : "" %>' class='fa fs18 text-danger fa-calendar'></i>
      </a>
    <% } %>
  </td>
  <% if (permiso > 1) { %>
    <td class="pl0 pr0">
      <select class="form-control no-model usuario_asignado">
        <% for (var i=0; i< usuarios.length; i++) { %>
          <% var u = usuarios.models[i] %>
          <option <%= (u.id == id_usuario_asignado)?"selected":"" %> value="<%= u.id %>"><%= u.get("nombre") %></option>
        <% } %>
      </select>
    </td>
  <% } %>
  <td class="data col-xxs-0 <%= clase %>">
    <% if (ID_PROYECTO == 3 && propiedad_id_tipo_operacion == 1) { %>
      <i class="iconito fa fa-envelope danger lg active"></i><span class="pr t-8 fs15 ml10"><%= propiedad_tipo_operacion %></span>
    <% } else if (ID_PROYECTO == 3 && propiedad_id_tipo_operacion == 2) { %>
      <i class="iconito fa fa-envelope warning lg active"></i><span class="pr t-8 fs15 ml10"><%= propiedad_tipo_operacion %></span>
    <% } else { %>

      <% if (id_origen == 9 || id_origen == 1 || id_origen == 6) { %>
        <i class="iconito fa fa-envelope info lg active"></i><span class="pr t-8 fs15 ml10">Formulario</span>
      <% } else if (id_origen == 30) { %>
        <i class="iconito fa fa-whatsapp success lg active"></i><span class="pr t-8 fs15 ml10">Whatsapp</span>
      <% } else if (id_origen == 31) { %>
        <i class="iconito fa fa-whatsapp danger lg active"></i><span class="pr t-8 fs15 ml10">Fuera de Línea</span>
      <% } %>
      
    <% } %>
  </td>
  <td class="data col-xxs-0 <%= clase %>">
    <span><%= (fecha_ult_operacion == "00/00/0000 00:00:00") ? "" : fecha_ult_operacion.replace(" ","<br/>") %></span>
    <?php /*<%= ((typeof respondido != undefined) && respondido == 1 && (typeof respondido_por != undefined) && !isEmpty(respondido_por) ) ? "<i data-toggle='tooltip' title='Respondido por: "+respondido_por+"' class='fa fa-share m-l'></i>" : "" %>*/ ?>
  </td>
  <td class="data col-xxs-0 <%= clase %>">
    <div class="pr">
      <div class="progress" style="margin-top:15px;height:12px;">
        <% if (tipo == 1) { %>
          <div class="progress-bar progress-bar-danger" style="width: 50%"></div>
          <div class="progress-bar progress-bar-gray" style="width: 50%"></div>
        <% } else { %>
          <div class="progress-bar progress-bar-gray" style="width: 50%"></div>
          <div class="progress-bar progress-bar-success" style="width: 50%"></div>
        <% } %>
      </div>    
      <div class="progress-mark"></div>
    </div>
  </td>
  <td class="pl0">
    <div class="btn-group dropdown w100p">
      <button class="btn btn-block btn-<%= color_estado %> btn-addon btn-addon2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><%= tipo_estado %> <span class="fs12"><i class="fa fa-caret-down"></i></span></button>
      <ul class="dropdown-menu pull-right">
        <% for(var i=0;i< consultas_tipos.length;i++) { %>
          <% var tt = consultas_tipos[i] %>
          <% if (tt.id != tipo) { %>
            <li><a href="javascript:void(0)" class="editar_tipo" data-tipo="<%= tt.id %>"><%= tt.nombre %></a></li>
          <% } %>
        <% } %>
      </ul>
    </div>
  </td>
</script>

<script type="text/template" id="pedido_dashboard_template">
  <% var clase = "bg-success" %>
  <% if (id_tipo_estado == 1 || id_tipo_estado == 2) { clase = "bg-danger" } %>
  <% if (id_tipo_estado == 3) { clase = "bg-warning" } %>
  <div class="panel consulta row-sm-same-height">
    <div class="col-sm-4 col-xs-12 col-sm-height">
      <div class="media">
        <span class="avatar <%= clase %> pull-left">
          <%= isEmpty(cliente) ? cliente_email.substr(0,1) : cliente.substr(0,1) %>
        </span>
        <div class="media-body">
          <div class="bold fs18"><%= cliente %></div>
          <% if (!isEmpty(cliente_email)) { %><div><%= cliente_email %></div><% } %>
          <% if (!isEmpty(cliente_telefono)) { %><div>Tel: <%= cliente_telefono %></div><% } %>
          <div><span class="bold"><%= fecha %></span> a las <span class="bold"><%= hora.substr(0,5) %></span> hs.</div>
        </div>
      </div>                        
    </div>
    <div class="col-sm-8 bl col-xs-12 col-sm-height">
      <div class="media">
        <% if (id_tipo_estado == 1 || id_tipo_estado == 2) { %>
          <img class="thumb-sm pull-left" src="/sistema/resources/images/pedido.png"/>
          <div class="media-body">
            <div class="bold">NUEVO PEDIDO</div>
            <div class="">
              Monto:
              <span class="consulta_precio text-danger">$ <%= total %></span>
            </div>
          </div>            
        <% } else { %>
          <% if (id_tipo_estado == 3) { %>
            <img class="thumb-sm pull-left" src="/sistema/resources/images/pendiente-1.png"/>
          <% } else { %>
            <img class="thumb-sm pull-left" src="/sistema/resources/images/like-venta.png"/>
          <% } %>
          <div class="media-body">
            <div class="bold">FELICITACIONES! Realizaste una nueva venta!</div>
            <div class="">
              Monto:
              <span class="consulta_precio <%= (id_tipo_estado == 3) ? 'text-warning':'text-success' %>">$ <%= total %></span>
            </div>
          </div>
        <% } %>
      </div>
      <div class="mt10">
        <% if (id_tipo_estado == 1) { %>
          <span class="bold">Estado: </span>Pendiente<br/>
          Comunicate con el cliente para finalizar la operaci&oacute;n
        <% } else if (id_tipo_estado == 2) { %>
          <span class="bold">Estado: </span>Autorizado<br/>
          El cliente podra finalizar el pedido.
        <% } else if (id_tipo_estado == 3) { %>
          <span class="bold">Estado: </span>Pendiente de Pago<br/>
          Solo resta que el cliente concrete el pago.
        <% } else if (id_tipo_estado == 9) { %>
          <span class="bold">Estado: </span>Pago a convenir<br/>
          Contacta al cliente para acordar el pago.
        <% } else { %>
          <% if (!isEmpty(codigo_autorizacion)) { %>
            <span class="bold">Medio de Pago: </span>MercadoPago<br/>
            <span class="bold">C&oacute;digo de Autorizaci&oacute;n: </span><%= codigo_autorizacion %><br/>
          <% } %>
        <% } %>
      </div>
      <div class="tar">
        <% if (id_tipo_estado == 1 || id_tipo_estado == 2) { %>
          <span class="fs14 mr10">En proceso</span>
          <i class="fa pr t5 fa-exclamation-circle text-danger fs26"></i>
        <% } else if (id_tipo_estado == 3) { %>
          <span class="fs14 mr10">Pendiente de pago</span>
          <i class="fa pr t5 fa-check-circle text-warning fs26"></i>
        <% } else if (id_tipo_estado == 8){ %>
          <span class="fs14 mr10">Pago en sucursal</span>
          <i class="fa pr t5 fa-check-circle text-success fs26"></i>
        <% } else if (id_tipo_estado == 4) { %>
          <span class="fs14 mr10">Pagado</span>
          <i class="fa pr t5 fa-check-circle text-success fs26"></i>
        <% } else if (id_tipo_estado == 5 || id_tipo_estado == 7) { %>
          <span class="fs14 mr10">Finalizado</span>
          <i class="fa pr t5 fa-check-circle text-success fs26"></i>
        <% } %>
      </div>                        
    </div>
  </div>
</script>

<script type="text/template" id="consulta_dashboard_template">
  <div class="panel consulta cp row-sm-same-height">
	  <div class="col-sm-4 col-xs-12 col-sm-height">
		  <div class="media">
			  <span class="avatar bg-<%= isEmpty(color_origen)?'info':color_origen %> pull-left"><%= isEmpty(nombre) ? email.substr(0,1) : nombre.substr(0,1) %></span>
			  <div class="media-body">
				  <div class="bold fs18"><%= nombre %></div>
				  <% if (!isEmpty(email)) { %><span><%= email %></span><br/><% } %>
				  <% if (!isEmpty(telefono)) { %><span class="">Tel: <%= telefono %></span><br/><% } %>
				  <% if (!isEmpty(celular)) { %><span class="">Cel: <%= celular %></span><br/><% } %>
				  <div><span class="bold"><%= fecha %></span> a las <span class="bold"><%= hora %></span> hs.</div>
			  </div>
		  </div>
	  </div>
	  <div class="col-sm-8 bl col-xs-12 col-sm-height">
		  <div class="media">
        <!-- TURNO EN GENERAL -->
        <% if (id_origen == 23) { %>
          <div class="media-body">
            <div class="bold">SOLICITUD DE TURNO</div>
            <div class=""><%= asunto %></div>
          </div>
        <!-- NUEVO USUARIO -->
        <% } else if (id_origen == 20) { %>
          <div class="media-body">
            <div class="bold">NUEVO USUARIO REGISTRADO</div>
          </div>

			  <% } else if (ID_PROYECTO == 3 && id_referencia != 0) { %>
  				<a href="app/#propiedad/<%= id_referencia %>" class="consulta_propiedad">
  				  <% if (!isEmpty(propiedad_path)) { %>
  					  <img class="customcomplete-image" src="<%= propiedad_path %>"/>
  				  <% } %>
  				</a>
  				<div class="media-body">
  					<div class="bold"><%= propiedad_nombre %></div>
  					<div class="">
              <%= propiedad_direccion %> <%= propiedad_ciudad %>
              <% if (id_empresa_relacion != id_empresa) { %>
                <span class="label bg-danger m-l-sm">Red</span>
              <% } %>
            </div>
  				</div>
        <% } else if (ID_PROYECTO == 11 && id_referencia != 0) { %>
          <a href="app/#viaje/<%= id_referencia %>" class="consulta_viaje">
            <% if (!isEmpty(viaje_path)) { %>
              <img class="customcomplete-image" src="<%= viaje_path %>"/>
            <% } %>
          </a>
          <div class="media-body">
            <div class="bold"><%= viaje_nombre %></div>
            <% if (!isEmpty(asunto)) { %>
              <div class=""><%= asunto %></div>
            <% } %>
          </div>
        <% } else if (id_entrada != 0) { %>
          <a href="app/#entrada/<%= id_entrada %>" class="consulta_entrada">
            <% if (!isEmpty(entrada_path)) { %>
              <img class="customcomplete-image" src="<%= entrada_path %>"/>
            <% } %>
          </a>
          <div class="media-body">
            <div class="bold"><%= entrada_nombre %></div>
          </div>        
        <% } else if (ID_PROYECTO == 2 && id_referencia != 0) { %>
          <a href="app/#articulo/<%= id_referencia %>" class="consulta_articulo">
            <% if (!isEmpty(articulo_path)) { %>
              <img class="customcomplete-image" src="<%= articulo_path %>"/>
            <% } %>
          </a>
          <div class="media-body">
            <div class="bold"><%= asunto %></div>
            <% if (id_origen == 25) { %>
              <div><%= articulo_nombre %></div>
            <% } else { %>
              <div class=""><%= (isEmpty(subtitulo)) ? "Consulta desde producto" : subtitulo %></div>
            <% } %>
          </div>
			  <% } else { %>
          <% if (!isEmpty(origen_path)) { %>
  				  <img class="thumb-sm pull-left" src="<%= origen_path %>"/>
          <% } %>
  				<div class="media-body">
  					<div class="bold">
              <%= (id_origen == 10)?"NUEVO PEDIDO": ((id_origen == 2) ? "REGISTRO NEWSLETTER" :"NUEVA CONSULTA") %>
            </div>
  					<% if (!isEmpty(asunto) && id_origen != 2) { %>
  						<div class=""><%= asunto %></div>
  					<% } %>
  				</div>
			  <% } %>
		  </div>
      <?php /*
		  <% if (id_origen != 10) { %>
			<div class="mt10">
			  <%= (texto.length > 300) ? texto.substr(0,300)+"..." : texto %>
			</div>
		  <% } else if (isEmpty(email_usuario)) { %>
			<div class="mt10">
				<span class="bold">Estado: </span>Pendiente<br/>
				Comunicate con el cliente para finalizar la operaci&oacute;n
			</div>
		  <% } %>
      */ ?>
      <div class="mt10">
        <%= (texto.length > 300) ? texto.substr(0,300)+"..." : texto %>
      </div>
		  <div class="tar">
  			<% if (!isEmpty(email_usuario)) { %>
  			  <span title="<%= email_usuario %>">
  				<span class="fs14 mr10">Respondido</span>
  				<i class="fa pr t5 fs26 fa-sign-out text-<%= color_origen %>"></i>
  			  </span>
  			<% } else { %>
  			  <a href="app/#cliente_acciones/<%= id_contacto %>">
  				  <span class="fs14 mr10">Responder</span>
  				  <i class="fa pr t5 fs26 fa-sign-out"></i>
  			  </a>
  			<% } %>
		  </div>
	  </div>
  </div>
</script>

<script type="text/template" id="comentario_dashboard_template">
  <div class="panel consulta cp row-sm-same-height">
    <div class="col-sm-4 col-xs-12 col-sm-height">
      <div class="media">
        <span class="avatar bg-info pull-left"><%= nombre.substr(0,1) %></span>
        <div class="media-body">
          <div class="bold fs18"><%= nombre %></div>
          <span><%= email %></span><br/>
          <div><span class="bold"><%= fecha %></span></div>
        </div>
      </div>                        
    </div>
    <div class="col-sm-8 bl col-xs-12 col-sm-height">
      <div class="media">
        <% if (id_entrada != 0) { %>
          <a href="app/#entrada/<%= id_entrada %>" class="consulta_entrada">
            <% if (!isEmpty(entrada_path)) { %>
              <img class="customcomplete-image" src="<%= entrada_path %>"/>
            <% } %>
          </a>
          <div class="media-body">
            <div class="bold"><%= entrada %></div>
          </div>        
        <% } %>
      </div>
      <div class="mt10">
        <%= (texto.length > 300) ? texto.substr(0,300)+"..." : texto %>
      </div>
    </div>
  </div>
</script>






<script type="text/template" id="consultas_detalle_template">
<div class="app-content-body fade-in-up">
    
    <div class="wrapper bg-light lter b-b" style="overflow: hidden">
        <div class="pull-left w-md">
            <div class="input-group">
                <input type="text" id="email_header_buscar" value="" name="" class="input-sm form-control" placeholder="Buscar">
                <span class="input-group-btn">
                    <button id="email_header_buscar_btn" class="btn btn-sm btn-default" type="button">Go!</button>
                </span>
            </div>
        </div>
    </div>
    
    <div class="hbox hbox-auto-xs hbox-auto-sm">
        <div class="col w-md bg-light dk b-r bg-auto">
          <div class="wrapper hidden-sm hidden-xs" id="email-menu">
            <ul class="nav nav-pills nav-stacked nav-sm">
              <li class="">
                <a href="app/#consultas">
                  Todos
                </a>
              </li>
              <% for(var i=0;i<origenes.length;i++) { %>
                <% var o = origenes[i]; %>
                <li class="<%= (id_origen == o.id)?"active":"" %>">
        					<a href="app/#consultas/<%= o.id %>">
        						<i class="fa fa-fw fa-circle text-<%= o.color %>"></i>
        						<%= o.nombre %>
        					</a>
                </li>
              <% } %>
            </ul>
          </div>
        </div>
        
        <div class="col">
          <div class="wrapper b-b">
            <h2 class="font-thin m-n"><%= nombre %></h2>
          </div>
          <% if (ID_PROYECTO == 3 && id_referencia != 0) { %>
            <div class="wrapper b-b cb oh">
              <a href="app/#propiedad/<%= id_referencia %>" class="consulta_propiedad">
                <img class="customcomplete-image" src="<%= propiedad_path %>"/>
                <div class="consulta_propiedad_texto">
                  <p><%= propiedad_nombre %></p>
                  <span><%= propiedad_direccion %></span>
                  <span><%= propiedad_ciudad %></span>
                </div>
              </a>
            </div>
          <% } %>
          <% if (ID_PROYECTO == 11 && id_referencia != 0) { %>
            <div class="wrapper b-b cb oh">
              <a href="app/#viaje/<%= id_referencia %>" class="consulta_viaje">
                <img class="customcomplete-image" src="<%= viaje_path %>"/>
                <div class="consulta_propiedad_texto">
                  <p><%= viaje_nombre %></p>
                </div>
              </a>
            </div>
          <% } %>
          <% if (ID_PROYECTO == 2 && id_referencia != 0) { %>
            <div class="wrapper b-b cb oh">
              <a href="app/#articulo/<%= id_referencia %>" class="consulta_propiedad">
                <img class="customcomplete-image" src="<%= articulo_path %>"/>
                <div class="consulta_propiedad_texto">
                  <p><%= articulo_nombre %></p>
                </div>
              </a>
            </div>
          <% } %> 
          <div class="wrapper b-b">
            <% if (!isEmpty(usuario)) { %>Para: <span class="label bg-warning m-l-sm"><%= usuario %></span><br/><% } %>
            Email: <a href="javascript:void(0)"><%= email %></a><br/>
            <% if (!isEmpty(telefono)) { %>Telefono: <%= telefono %><br/><br/><% } %>
            <% if (!isEmpty(celular)) { %>Celular: <%= celular %><br/><% } %>
            <% if (!isEmpty(ciudad)) { %>Ciudad: <%= ciudad %><br/><% } %>
          </div>
		  <div class="wrapper b-b">
			Asunto: <%= asunto %>
		  </div>
          <div class="wrapper">
            <%= texto %>
          </div>

          <% if (!isEmpty(texto_respuesta)) { %>
            <div class="m-l m-t">
              <div class="panel b-a">
                <div class="panel-heading pos-rlt">
                  <span class="arrow left pull-up"></span>
                  <span class="text-muted m-l-sm pull-right">
                    <%= email_fecha %>
                  </span>
                  <%= texto_respuesta %>
                  <% if (!isEmpty(email_archivo_adjunto)) { %>
                    <br/>
                    <i class="fa fa-paperclip"></i> 
                    <a class="text-info" target="_blank" href="/sistema/<%= email_archivo_adjunto %>">Archivo adjunto</a>
                  <% } %>
                </div>
              </div>
            </div>
          <% } %>

          <div class="wrapper">
            <button class="btn btn-info responder">Responder</button>
            <% if (ID_PROYECTO == 3 && id_referencia != 0) { %>
              <button class="btn btn-primary buscar_similares">Buscar similares</button>
            <% } %>
            <a href="app/#consultas" class="btn btn-default">Volver</a>
            <button class="pull-right btn btn-danger eliminar">Eliminar</button>
          </div>
        </div>
    </div>
</div>  
</script>



<script type="text/template" id="consulta_edit_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    <%= (!isEmpty(asunto)) ? asunto : ((id == undefined) ? "<?php echo lang(array("es"=>"Nueva Consulta","en"=>"New Contact")); ?>" : "<?php echo lang(array("es"=>"Consulta","en"=>"Contact")); ?>") %>
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <form class="panel-body" autocomplete="off">
    <div class="form-group">
      <input type="text" placeholder="<?php echo lang(array("es"=>"Nombre y Apellido","en"=>"Full Name")); ?>" autocomplete="off" id="consulta_cliente_nombre" name="nombre" class="form-control"/>
    </div>  
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <input type="text" placeholder="<?php echo lang(array("es"=>"Tel&eacute;fono","en"=>"Telephone")); ?>" autocomplete="off" id="consulta_cliente_telefono" name="telefono" class="form-control"/>
        </div>  
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <input type="text" placeholder="<?php echo lang(array("es"=>"Email","en"=>"Email Address")); ?>" id="consulta_cliente_email" autocomplete="off" name="email" class="form-control"/>
        </div>  
      </div>
    </div>
    <?php /*
    <% if (ID_PROYECTO == 3) { %>
      <div class="form-group">
        <input type="text" placeholder="Propiedad" id="consulta_propiedad" value="<%= propiedad_nombre %>" class="form-control"/>
      </div>
    <% } %>
    */ ?>
    <% if (ID_PROYECTO == 1 || ID_PROYECTO == 2) { %>
      <div class="form-group">
        <input type="text" placeholder="<?php echo lang(array("es"=>"Interesado en producto..","en"=>"Interested in product..")); ?>" id="consulta_articulo" autocomplete="off" value="<%= articulo_nombre %>" class="form-control"/>
      </div>
    <% } %>
    <div class="row">
      <div class="col-xs-6">
        <div class="form-group">
          <div class="input-group">
            <input type="text" placeholder="<?php echo lang(array("es"=>"Fecha","en"=>"Date")); ?>" id="consulta_fecha" autocomplete="off" value="<%= fecha %>" class="form-control" name="fecha"/>
            <span class="input-group-btn">
              <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
            </span>        
          </div>
        </div>
      </div>  
      <div class="col-xs-6">
        <div class="form-group">
          <div class="btn-group">
            <label data-id_origen="4" data-toggle="tooltip" title="<?php echo lang(array("es"=>"Tel&eacute;fono","en"=>"Telephone")); ?>" class="btn btn-default active btn-info id_origen"><i class="fa fa-phone"></i></label>
            <label data-id_origen="5" data-toggle="tooltip" title="Email" class="btn btn-default id_origen"><i class="fa fa-envelope"></i></label>
            <label data-id_origen="26" data-toggle="tooltip" title="Facebook" class="btn btn-default id_origen"><i class="fa fa-facebook"></i></label>
            <label data-id_origen="3" data-toggle="tooltip" title="Personal" class="btn btn-default id_origen"><i class="fa fa-users"></i></label>
            <label data-id_origen="27" data-toggle="tooltip" title="Whatsapp" class="btn btn-default id_origen"><i class="fa fa-whatsapp"></i></label>
          </div>
        </div>
      </div>  
    </div>
    <div class="form-group">
      <textarea name="texto" class="form-control h100" placeholder="<?php echo lang(array("es"=>"Escriba aqui la consulta...","en"=>"Write the query here...")); ?>" id="consulta_texto"><%= texto %></textarea>
    </div>
    <?php /*
    <% if (control.check("propiedades")>0) { %>
      <div class="form-group">
        <div class="checkbox">
          <label class="i-checks">
            <input type="checkbox" checked=""><i></i> Enviar ficha de propiedad al contacto al guardar.
          </label>
        </div>                  
      </div>
    <% } %>
    */ ?>
  </form>
  <div class="panel-footer clearfix">
    <button class="cerrar_lightbox btn btn-default"><?php echo lang(array("es"=>"Cerrar","en"=>"Close")); ?></button>
    <button class="btn guardar pull-right btn-success"><?php echo lang(array("es"=>"Guardar","en"=>"Save")); ?></button>
  </div>
</div>
</script>

<script type="text/template" id="crear_consulta_timeline_template">
  <div class="panel panel-default mb0">
    <ul class="nav nav-tabs nav-tabs-2" role="tablist">
      <% if (ID_EMPRESA == 228) { %>
        <% var active_tab = "tab_tarea" %>
        <li class="active">
          <a id="tab3_link" href="#tab_tarea" role="tab" data-toggle="tab"><i class="fa fa-clock-o text-muted"></i> Tarea</a>
        </li>
        <li>
          <a id="tab2_link" href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-commenting text-muted"></i> SMS</a>
        </li>
        <li>
          <a id="tab_link_observacion" href="#tab_observacion" role="tab" data-toggle="tab"><i class="fa fa-file-text text-muted"></i> Nota</a>
        </li>
      <% } else { %>
        <% var active_tab = "tab1" %>
        <li class="active">
          <a id="tab1_link" href="#tab1" role="tab" data-toggle="tab"><i class="fa fa-envelope text-muted"></i> Email</a>
        </li>
        <% if (mostrar_sms) { %>
          <li>
            <a id="tab2_link" href="#tab2" role="tab" data-toggle="tab"><i class="fa fa-commenting text-muted"></i> SMS</a>
          </li>
        <% } %>
        <% if (mostrar_whatsapp) { %>
          <li>
            <a id="tab3_link" href="#tab3" role="tab" data-toggle="tab"><i class="fa fa-whatsapp text-muted"></i> Whatsapp</a>
          </li>
        <% } %>
        <% if (mostrar_tarea) { %>
          <li>
            <a id="tab3_link" href="#tab_tarea" role="tab" data-toggle="tab"><i class="fa fa-clock-o text-muted"></i> Tarea</a>
          </li>
        <% } %>
        <li>
          <a id="tab_link_observacion" href="#tab_observacion" role="tab" data-toggle="tab"><i class="fa fa-file-text text-muted"></i> Nota</a>
        </li>
        <?php /*
        // ESTE TAB ES DISTINTO, CREA UNA NUEVA CONSULTA DE TIPO "NOTA"
        <li>
          <a id="tab_link_nota" href="#tab_nota" role="tab" data-toggle="tab"><i class="fa fa-file-text text-muted"></i> Nota</a>
        </li> */ ?>
      <% } %>
    </ul>
    <div class="tab-content">
      <div id="tab1" class="tab-pane panel-body <%= (active_tab=='tab1')?'active':'' %>">
        <div class="form-group">
          <input type="text" id="consulta_email_asunto" placeholder="Asunto" class="form-control"/>
        </div>
        <div class="form-group">
          <textarea id="consulta_email_texto"></textarea>
        </div>      
        <div class="form-group clearfix">
          <div class="fl">
            <div class="w200">
              <span class="btn btn-default fileinput-button">
                <i class="glyphicon glyphicon-folder-open m-r-xs"></i>
                <span>Adjuntar archivos</span>
                <input id="fileupload_timeline" type="file" name="files[]" multiple>
              </span>
              <div id="progress_timeline" class="progress" style="display: none">
                <div class="progress-bar progress-bar-success"></div>
              </div>
              <div id="files_timeline" class="files"></div>
            </div>
          </div>
          <button class="btn btn-pd btn-info guardar_email fr">Enviar</button>
        </div>
        <% if (alerta_email) { %>
          <div class="form-group clearfix">
            <div class="alert alert-warning alert-dismissable">
              <i class="fa fa-warning"></i>
              Atenci&oacute;n! La persona no tiene cargada un email de contacto.
            </div>
          </div>
        <% } %>
      </div>

      <% if (mostrar_sms) { %>
        <div id="tab2" class="tab-pane panel-body <%= (active_tab=='tab2')?'active':'' %>">
          <div class="form-group">
            <% if (telefonos.length > 0) { %>
              <div class="form-group fl w200">
                <select id="consulta_sms_telefono" class="form-control">
                  <% for(var k=0;k< telefonos.length; k++) { %>
                    <% var telefono = telefonos[k] %>
                    <option><%= telefono %></option>
                  <% } %>
                </select>
              </div>
            <% } %>
            <label class="control-label fr">
              <span id="consulta_sms_title">0</span> de <span>160</span>
            </label>
            <textarea data-max="160" data-id="consulta_sms_title" id="consulta_sms" class="form-control h100 no-model text-remain"></textarea>
          </div>
          <div class="form-group clearfix">
            <% if (alerta_celular) { %>
              <div class="alert alert-warning mb0 p5 fl alert-dismissable">
                <i class="fa fa-warning"></i>
                Atenci&oacute;n! La persona no tiene cargada un celular de contacto.
              </div>
            <% } %>
            <button class="btn btn-pd btn-info guardar_sms fr">Guardar</button>
          </div>
        </div>
      <% } %>

      <% if (mostrar_whatsapp) { %>
        <div id="tab3" class="tab-pane panel-body <%= (active_tab=='tab3')?'active':'' %>">
          <div class="form-group">
            <textarea id="consulta_whatsapp" placeholder="Escribe aqui tu mensaje..." class="form-control h100 no-model"></textarea>
          </div>
          <div class="form-group clearfix">
            <% if (alerta_celular) { %>
              <div class="alert alert-warning mb0 p5 fl alert-dismissable">
                <i class="fa fa-warning"></i>
                Atenci&oacute;n! La persona no tiene cargada un celular de contacto.
              </div>
            <% } %>
            <button class="btn btn-pd btn-info enviar_whatsapp fr">Enviar</button>
          </div>
        </div>
      <% } %>

      <% if (mostrar_tarea) { %>
        <div id="tab_tarea" class="tab-pane panel-body <%= (active_tab=='tab_tarea')?'active':'' %>">
          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
                <label class="control-label">Tarea</label>
                <div class="input-group">
                  <select id="consulta_tarea_asuntos" class="select w100p"></select>
                  <span class="input-group-btn">
                    <button tabindex="-1" class="btn btn-info agregar_asunto">+</button>  
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-7">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Fecha</label>
                    <div class="input-group">
                      <input placeholder="Fecha" type="text" class="form-control no-model" id="consulta_tarea_fecha"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>        
                    </div>
                  </div>
                </div>
                <div class="col-md-6 <%= (ID_EMPRESA == 228)?"":"dn" %>">
                  <div class="form-group">
                    <label class="control-label">Promesa</label>
                    <div class="input-group">
                      <input placeholder="Fecha" type="text" class="form-control no-model" id="consulta_tarea_fecha_visto"/>
                      <span class="input-group-btn">
                        <button tabindex="-1" type="button" class="btn btn-default btn-cal"><i class="glyphicon glyphicon-calendar"></i></button>
                      </span>        
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <textarea id="consulta_tarea_texto" placeholder="Escribe aqui la tarea para realizar..." class="form-control no-model h100"></textarea>
          </div>
          <div class="form-group clearfix tar">
            <button class="btn btn-pd btn-info guardar_tarea">Guardar</button>
          </div>
        </div>
      <% } %>
      <div id="tab_nota" class="tab-pane panel-body <%= (active_tab=='tab_nota')?'active':'' %>">
        <div class="form-group">
          <textarea id="consulta_nota" placeholder="Escribe aqui alguna nota u observacion..." class="form-control no-model h100"></textarea>
        </div>
        <div class="form-group tar">
          <button class="btn btn-pd btn-info guardar_nota fr">Guardar</button>
        </div>
      </div>
      <div id="tab_observacion" class="tab-pane panel-body <%= (active_tab=='tab_observacion')?'active':'' %>">
        <div class="form-group">
          <textarea id="consulta_observacion" placeholder="Escribe aqui alguna nota u observacion..." class="form-control no-model h100"><%= nota %></textarea>
        </div>
        <div class="form-group tar">
          <button class="btn btn-pd btn-info guardar_observacion fr">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="consulta_timeline_template">
  <% var dias_desde_consulta = moment(moment()).diff(moment(fecha, "DD/MM/YYYY"), 'days'); %>

  <?php 
  // CREACION DE USUARIO
  ?>
  <% if (id_origen == 20) { %>
    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <i class="fa fa-user"></i>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light">
        <span class="arrow left"></span>
        <div>
          <div class="pb5">
            <?php echo lang(array("es"=>"Se cre&oacute; el usuario:","en"=>"Creation:")); ?>
            <b><%= (tipo == 1) ? usuario.ucwords() : nombre.ucwords() %></b>
            <span class="text-muted fs13 pull-right">
              <i class="fa fa-clock-o"></i>
              <%= mostrar_fecha(fecha,hora) %>
            </span>
          </div>
        </div>
      </div>
    </div>
  <?php // FIN DE CREACION DE USUARIO ?>

  <?php 
  // NOTIFICACION DEL SISTEMA. CAMBIO DE USUARIO. CAMBIO DE ESTADO
  ?>
  <% } else if (id_origen == 32) { %>
    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <i class="fa fa-user"></i>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light">
        <span class="arrow left"></span>
        <div>
          <div class="pb5">
            <b class="mr5"><%= asunto %>:</b>
            <%= texto.replace(">","<i class='fa fa-caret-right ml5 mr5'></i>") %>
            <%= (!isEmpty(custom_1)) ? "<br/>"+nl2br(custom_1) : "" %>
            <span class="text-muted fs13 pull-right">
              <i class="fa fa-clock-o"></i>
              <%= mostrar_fecha(fecha,hora) %>
            </span>
          </div>
        </div>
      </div>
    </div>
  <?php // FIN DE NOTIFICACION DEL SISTEMA ?>

  <?php 
  // EMAIL DE INTERES PROPIEDAD
  ?>
  <% } else if (id_origen == 28) { %>
    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <i class="fa fa-envelope"></i>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light pb10">
        <span class="arrow left"></span>
        <div>
          <div class="pb5">
            Se envi&oacute; un email de interes
            <b><%= (tipo == 1) ? usuario.ucwords() : nombre.ucwords() %></b>
            <span class="text-muted fs13 pull-right">
              <% if (!isEmpty(fecha_visto)) { %>
                <i class="fa fa-eye text-info mr5" data-toggle="tooltip" title="<%= fecha_visto %>"></i>
              <% } %>
              <i class="fa fa-clock-o"></i>
              <%= mostrar_fecha(fecha,hora) %>
            </span>
          </div>
          <div class="dt pb5">
            <div class="dtc vam">
              <a href="app/#propiedad/<%= id_referencia %>" class="consulta_propiedad">
                <% if (!isEmpty(propiedad_path)) { %>
                  <img class="customcomplete-image fn" src="<%= propiedad_path %>"/>
                <% } %>
              </a>
            </div>
            <div class="dtc vam">
              <span class="h4"><%= asunto %></span>
              <br/><span class="text-muted fs14"><%= propiedad_direccion %> | <%= propiedad_ciudad %></span>
              <% if (id_empresa_relacion != id_empresa) { %>
                <span class="label bg-danger m-l-sm">Red</span>
              <% } %>
            </div>
          </div>
        </div>
      </div>
    </div>  
  <?php // FIN DE EMAIL DE INTERES PROPIEDAD ?>

  <?php 
  // COMPRA WEB
  ?>
  <% } else if (id_origen == 18) { %>
    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <i title="Reserva" class="fa fa-shopping-cart"></i>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light">
        <span class="arrow left"></span>
        <div>
          <div class="pb5">
            <b><%= (tipo == 1) ? usuario.ucwords() : nombre.ucwords() %></b>
            realiz&oacute; una compra:
            <span class="text-muted fs13 pull-right">
              <i class="fa fa-clock-o"></i>
              <%= mostrar_fecha(fecha,hora) %>
            </span>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="dt pb5 w100p">
          <div class="dtc vam">
            <span class="h4 cp ver_compra"><%= texto %></span>
          </div>
          <div class="dtc vam tar">
            <a href="app/#pedido/<%= id_relacion %>" class="ver_compra btn btn-white">Ver compra</a>
          </div>
        </div>
      </div>
    </div>
  <?php // FIN DE COMPRA WEB ?>

  <?php 
  // RESERVA DE VIAJE 
  ?>
  <% } else if (id_origen == 13) { %>
    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <i title="Reserva" class="fa fa-suitcase"></i>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light">
        <span class="arrow left"></span>
        <div>
          <div class="pb5">
            <b><%= (tipo == 1) ? usuario.ucwords() : nombre.ucwords() %></b>
            realiz&oacute; una reserva:
            <span class="text-muted fs13 pull-right">
              <i class="fa fa-clock-o"></i>
              <%= mostrar_fecha(fecha,hora) %>
            </span>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="dt pb5">
          <div class="dtc vam">
            <a href="javascript:void(0)" class="consulta_viaje ver_reserva">
              <% if (!isEmpty(viaje_path)) { %>
                <img class="customcomplete-image fn" src="<%= viaje_path %>"/>
              <% } %>
            </a>
          </div>
          <div class="dtc vam">
            <span class="h4 cp ver_reserva"><%= viaje_nombre %></span>
          </div>
        </div>
      </div>
    </div>
  <?php // FIN DE RESERVA DE VIAJE ?>

  <!-- TURNO EN GENERAL -->
  <% } else if (id_origen == 23) { %>
    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <i title="Turno" class="fa fa-calendar"></i>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light">
        <span class="arrow left"></span>
        <div>
          <div class="pb5">
            <b><%= (tipo == 1) ? usuario.ucwords() : nombre.ucwords() %></b>
            solicit&oacute; un turno
            <span class="text-muted fs13 pull-right">
              <i class="fa fa-clock-o"></i>
              <%= mostrar_fecha(fecha,hora) %>
            </span>
          </div>
          <div class="dt pb5">
            <div class="dtc vam">
              <span class="h4"><%= asunto %></span>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="opciones tar">
          <a href="javascript:void()" onclick="workspace.imprimir_reporte('/sistema/turnos/function/ver_pdf/<%= id_referencia %>/<%= ID_EMPRESA %>')" class="btn btn-white">
            <i class="fa fa-print"></i>&nbsp;&nbsp;Imprimir
          </a>
        </div>
      </div>
    </div>

  <!-- TAREA -->
  <% } else if (id_origen == 17) { %>
    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <i title="Tarea" class="fa fa-calendar"></i>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light">
        <span class="arrow left"></span>
        <div class="pb5">
          <b><%= asunto %></b>
          <span class="text-muted fs13 m-l"><i class="fa fa-user m-r-xs"></i> <%= usuario.ucwords() %></span>
          <span class="text-muted fs13 pull-right">
            <i class="fa fa-clock-o"></i>
            <%= mostrar_fecha(fecha,hora) %>
            <% if (ID_EMPRESA == 228) { %>
              | Promesa: <%= fecha_visto %>
            <% } %>
          </span>
        </div>        
      </div>
      <div class="panel-body">
        <div class="consulta_timeline_texto"><%= nl2br(texto) %></div>
      </div>
      <div class="panel-footer">
        <a href="javascript:void(0)" class="btn ver_tarea btn-white">
          <i class="fa fa-pencil m-r-xs"></i>
          Ver Tarea
        </a>
      </div>
    </div>

  <!-- TURNO MEDICO -->
  <% } else if (id_origen == 16) { %>
    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <i title="Turno m&eacute;dico" class="fa fa-stethoscope"></i>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light">
        <span class="arrow left"></span>
        <div class="pb5">
          <span class="h4">
            <% if (mostrar_paciente) { %>
              <a class="text-info" href="app/#paciente_acciones/<%= id_contacto %>"><%= paciente %></a>
            <% } else { %>
              <%= asunto %>
            <% } %>
          </span>
          <span class="text-muted fs13 pull-right">
            <i class="fa fa-clock-o"></i>
            <%= mostrar_fecha(fecha,hora) %>
          </span>
        </div>
      </div>
      <div class="panel-body">
        <div class="consulta_timeline_texto"><%= nl2br(texto) %></div>
        <div class="dn editar_texto_container">
          <textarea id="consulta_timeline_edicion_texto" name="texto" class="form-control h100"><%= texto %></textarea>
          <div class="tar m-t-xs">
            <button class="btn btn-default descartar_texto">Cancelar</button>
            <button class="btn btn-success guardar_texto">Guardar</button>
          </div>
        </div>
        <div class="tar">
          <a href="javascript:void(0)" class="btn editar_texto btn-white">
            <i class="fa fa-pencil "></i>
            Editar
          </a>
          <% if (estado_turno == 0) { %>
            <a href="javascript:void(0)" class="btn realizar_turno btn-success">
              <i class="fa fa-check"></i>
              Realizado
            </a>
          <% } %>
        </div>
      </div>
    </div>

  <% } else { %>

    <a class="pull-left thumb-sm bg-info avatar avatar-texto xs m-l-n-md">
      <% if (id_origen == 14) { %>
        <i title="Nota" class="fa fa-file-text"></i>
      <% } else if (id_origen == 15) { %>
        <i title="SMS" class="fa fa-commenting"></i>
      <% } else if (id_origen == 5 || id_origen == 9 || id_origen == 10) { %>
        <i title="Email" class="fa fa-envelope"></i>
      <% } else if (id_origen == 4) { %>
        <i title="Telefono" class="fa fa-phone"></i>
      <% } else if (id_origen == 26) { %>
        <i title="Facebook" class="fa fa-facebook"></i>
      <% } else if (id_origen == 27 || id_origen == 30 || id_origen == 31) { %>
        <i title="Whatsapp" class="fa fa-whatsapp"></i>
      <% } else if (id_origen == 3) { %>
        <i title="Personal" class="fa fa-user"></i>
      <% } else { %>
        <i class="fa fa-user"></i>
      <% } %>
    </a>
    <div class="m-l-lg panel b-a">
      <div class="panel-heading clearfix pos-rlt b-b b-light">
        <span class="arrow left"></span>
        <div>
          <div class="pb5">
            <b><%= (tipo == 1) ? usuario.ucwords() : nombre.ucwords() %></b>
            <% if (id_origen == 10) { %>
              <?php echo lang(array("es"=>"est&aacute; interesado en","en"=>"is interested in")); ?>
            <% } else if (id_origen == 3) { %>
              <?php echo lang(array("es"=>"se contact&oacute;","en"=>"spoke")); ?>
            <% } else if (id_origen == 4) { %>
              <?php echo lang(array("es"=>"llam&oacute;","en"=>"called")); ?>
            <% } else { %>
              <?php echo lang(array("es"=>"escribi&oacute;","en"=>"wrote")); ?>
            <% } %>
            <%= (id_referencia != 0) ? " por:":":" %>
            <span class="text-muted fs13 pull-right">

              <% if ((id_origen == 5 || id_origen == 9 || id_origen == 10) && !isEmpty(fecha_visto)) { %>
                <i class="fa fa-eye text-info mr5" data-toggle="tooltip" title="<%= fecha_visto %>"></i>
              <% } %>

              <i class="fa fa-clock-o"></i>
              <%= mostrar_fecha(fecha,hora) %>
              <% if (ID_EMPRESA == 228) { %>
                | Promesa: <%= fecha_visto %>
              <% } %>
            </span>
          </div>
          <div class="dt pb5">
            <% if (ID_PROYECTO == 2 && id_referencia != 0) { %>
              <div class="dtc vam">
                <a href="app/#articulo/<%= id_referencia %>" class="consulta_articulo">
                  <% if (!isEmpty(articulo_path)) { %>
                    <img class="customcomplete-image fn" src="<%= articulo_path %>"/>
                  <% } %>
                </a>
              </div>
              <div class="dtc vam">
                <span class="h4"><%= asunto %></span>
              </div>
            <% } else if (ID_PROYECTO == 3 && id_referencia != 0) { %>
              <div class="dtc vam">
                <a href="app/#propiedad/<%= id_referencia %>" class="consulta_propiedad">
                  <% if (!isEmpty(propiedad_path)) { %>
                    <img class="customcomplete-image fn" src="<%= propiedad_path %>"/>
                  <% } %>
                </a>
              </div>
              <div class="dtc vam">
                <span class="h4"><%= asunto %></span>
                <br/><span class="text-muted fs14"><%= propiedad_direccion %> | <%= propiedad_ciudad %></span>
                <% if (id_empresa_relacion != id_empresa) { %>
                  <span class="label bg-danger m-l-sm">Red</span>
                <% } %>
              </div>
            <% } else if (ID_PROYECTO == 11 && id_referencia != 0) { %>
              <div class="dtc vam">
                <a href="app/#viaje/<%= id_referencia %>" class="consulta_viaje">
                  <% if (!isEmpty(viaje_path)) { %>
                    <img class="customcomplete-image fn" src="<%= viaje_path %>"/>
                  <% } %>
                </a>
              </div>
              <div class="dtc vam">
                <span class="h4"><%= viaje_nombre %></span>
              </div>
            <% } else if (!isEmpty(asunto)) { %>
              <div class="dtc vam">
                <span class="h4">
                  <%= asunto %>
                </span>
                <% if ((MILLING == 1) && !isEmpty(subtitulo)) { %>
                  <br/>To: <span class="h5"><%= subtitulo %></span>
                <% } %>
              </div>
            <% } %>
          </div>
        </div>
      </div>
      <% if (id_origen == 1 || id_origen == 5 || id_origen == 9 || id_origen == 10 || id_origen == 27) { %>
        <div class="panel-body">
          <div class="consulta_timeline_texto">
            <% if (blur == 1 && dias_desde_consulta < 5) { %>
              <span style="color: transparent;text-shadow: 0 0 5px rgba(0,0,0,0.5);">**********************************************</span>
            <% } else { %>
              <%= (isHtml(texto)) ? texto : nl2br(texto) %>
            <% } %>
          </div>
          <div class="oh">
            <div class="fl">
              <% for(var k=0;k< adjuntos.length; k++) { %>
                <% var adj = adjuntos[k] %>
                <div>
                  <a href="<%= adj.path %>" target="_blank" class="link"><i class="fa fa-file"></i> <%= adj.path %></a>
                </div>
              <% } %>
            </div>
            <% if (id_origen != 27) { %>
              <a href="javascript:void(0)" class="btn responder_email btn-white fr">
                <i class="fa fa-mail-forward"></i>
                Responder
              </a>
            <% } %>
          </div>
        </div>
      <% } else { %>
        <div class="panel-body">



          <div class="consulta_timeline_texto">
            <% if (blur == 1 && dias_desde_consulta < 5) { %>
              <span style="color: transparent;text-shadow: 0 0 5px rgba(0,0,0,0.5);">**********************************************</span>
            <% } else { %>
              <%= nl2br(texto) %>
            <% } %>
          </div>
          <div class="dn editar_texto_container">
            <textarea id="consulta_timeline_edicion_texto" name="texto" class="form-control h100"><%= texto %></textarea>
            <div class="tar m-t-xs">
              <button class="btn btn-default descartar_texto">Cancelar</button>
              <button class="btn btn-success guardar_texto">Guardar</button>
            </div>
          </div>
          <div class="opciones tar">
            <a class="expand-link">
              <?php echo lang(array(
                "es"=>"+ M&aacute;s opciones",
                "en"=>"+ More options",
              )); ?>
            </a>
          </div>
        </div>
        <div class="panel-body expand tar">
          <!-- NOTA -->
          <% if (id_origen == 14) { %>
            <a href="javascript:void(0)" class="btn editar_texto btn-white">
              <i class="fa fa-pencil"></i> Editar
            </a>
            <a href="javascript:void(0)" class="btn eliminar btn-white">
              <i class="fa fa-trash"></i>
            </a>
          <% } %>
        </div>
      <% } %>
      <% if (children.length > 0) { %>
        <% for (var i=0; i< children.length; i++) { %>
          <% var hijo = children[i] %>
          <div class="panel-body b-b" style="background-color: #f9f9f9">
            <div class="pb5">
              <b><%= hijo.usuario.ucwords() %></b> respondi&oacute;:
              <span class="text-muted fs13 pull-right">
                <i class="fa fa-clock-o"></i>
                <%= mostrar_fecha(hijo.fecha,hijo.hora) %>
              </span>
            </div>
            <div><%= nl2br(hijo.texto) %></div>
            <div>
              <% for(var k=0;k< hijo.adjuntos.length; k++) { %>
                <% var adj = hijo.adjuntos[k] %>
                <div>
                  <a href="<%= adj.path %>" target="_blank" class="link"><i class="fa fa-file"></i> <%= adj.path %></a>
                </div>
              <% } %>
            </div>
          </div>
        <% } %>
      <% } %>
    </div>
  <% } %>
</script>


<script type="text/template" id="email_template">
<div class="panel panel-default mb0">
  <div class="panel-heading font-bold">
    Enviar Email
    <i class="pull-right cerrar_lightbox glyphicon glyphicon-remove cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-horizontal">
      <div class="form-group">
        <div class="col-sm-3 col-md-2 col-xs-12">
          <label class="control-label">Para:</label>
        </div>
        <div class="col-sm-9 col-md-10 col-xs-12">
          <input type="text" name="email" id="email_nombre" value="<%= email %>" class="form-control"/>
        </div>
      </div>      
      <div class="form-group">
        <div class="col-sm-3 col-md-2 col-xs-12">
          <label class="control-label">Asunto:</label>
        </div>
        <div class="col-sm-9 col-md-10 col-xs-12">
          <input type="text" name="asunto" id="email_asunto" value="<%= asunto %>" class="form-control"/>
        </div>
      </div>      
      <?php /*
      <div class="form-group">
        <div class="col-sm-3 col-md-2 col-xs-12">
          <label class="control-label">Asunto:</label>
        </div>
        <div class="col-sm-9 col-md-10 col-xs-12">
          <div class="input-group">
            <input type="text" name="asunto" id="email_asunto" value="<%= asunto %>" class="form-control"/>
            <div class="input-group-btn dropdown">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Plantillas <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right">
                <li><a class="cargar_plantilla" href="javascript:void(0)">Cargar</a></li>
                <li><a class="guardar_plantilla" href="javascript:void(0)">Guardar</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>*/ ?>

      <div class="form-group col-xs-12">
        <span class="btn btn-default fileinput-button">
          <i class="glyphicon glyphicon-folder-open m-r-xs"></i>
          <span>Adjuntar archivos</span>
          <input id="fileupload" type="file" name="files[]" multiple>
        </span>
        <div id="progress" class="progress" style="display: none">
          <div class="progress-bar progress-bar-success"></div>
        </div>
        <div id="files" class="files"></div>
      </div>

      <% if (links_adjuntos.length > 0) { %>
        <div class="form-group">
          <div class="col-sm-3 col-md-2 col-xs-12">
            <label class="control-label">Fichas:</label>
          </div>
          <div class="col-sm-9 col-md-10 col-xs-12">
            <% for (var i=0;i< links_adjuntos.length;i++) { %>
              <% var adjunto = links_adjuntos[i]; %>
              <button data-position="<%= i %>" class="btn btn-default m-b"><%= adjunto.nombre %><i class="ml5 eliminar_adjunto glyphicon glyphicon-remove"></i></button>
            <% } %>
          </div>
        </div>
      <% } %>

      <div class="form-group">
        <div class="col-xs-12">
          <textarea name="texto" id="email_texto"><%= texto %></textarea>
        </div>
      </div>      
    </div>
  </div>
  <div class="panel-footer clearfix">
    <button class="btn guardar pull-right btn-info btn-addon">
      <i class="fa fa-send"></i><span>Enviar</span>
    </button>
  </div>
</div>
</script>



<script type="text/template" id="asuntos_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuracion","en"=>"Configuration")); ?>
      / <b>Asuntos</b>
    </h1>
  </div>
  <div class="wrapper-md ng-scope">
    <div class="panel panel-default">
    
      <div class="panel-heading oh">
        <div class="row">
          <div class="col-md-6 col-lg-3 sm-m-b">
            <div class="search_container"></div>
          </div>
          <div class="col-md-6 col-lg-offset-3 col-lg-6 text-right">
            <a class="btn btn-info btn-addon" href="app/#asunto"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="b-a table-responsive">
          <table id="asuntos_table" class="table table-striped sortable m-b-none default footable">
            <thead>
              <tr>
                <th style="width:20px;">
                  <label class="i-checks m-b-none">
                    <input class="esc sel_todos" type="checkbox"><i></i>
                  </label>
                </th>
                <th class="sorting" data-sort-by="nombre">Nombre</th>
                <th class="w100"></th>
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


<script type="text/template" id="asuntos_item">
  <td>
    <label class="i-checks m-b-none">
      <input class="esc check-row" value="<%= id %>" type="checkbox"><i></i>
    </label>
  </td>
  <td class="ver"><span class='text-info'><%= nombre %></span></td>
  <td class="p5 td_acciones">
    <% if (id_empresa > 0) { %>
      <i title="Activo" class="fa-check iconito fa activo <%= (activo == 1)?"active":"" %>"></i>
      <div class="btn-group dropdown ml10">
        <button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-plus"></i>
        </button>   
        <ul class="dropdown-menu pull-right">
          <li><a href="javascript:void(0)" class="duplicar" data-id="<%= id %>">Duplicar</a></li>
          <li><a href="javascript:void(0)" class="delete" data-id="<%= id %>">Eliminar</a></li>
        </ul>
      </div>
    <% } %>
  </td>
</script>

<script type="text/template" id="asuntos_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i><?php echo lang(array("es"=>"Configuracion","en"=>"Configuration")); ?>
    / Asuntos
    / <b><%= (id == undefined) ? 'Nuevo' : nombre %></b>
  </h1>
</div>
<div class="wrapper-md ng-scope">
  <div class="centrado rform">
    <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-8">
        <div class="panel panel-default">
          <div class="panel-body">
          
            <div class="padder">
              <div class="form-group">
                <label class="control-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" id="asuntos_nombre" value="<%= nombre %>"/>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Color</label>
                    <input type="text" name="color" class="form-control" id="asuntos_color" value="<%= color %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Orden</label>
                    <input type="text" name="orden" class="form-control" id="asuntos_orden" value="<%= orden %>"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <% if (id_empresa > 0) { %>
          <button class="btn guardar btn-success">Guardar</button>
        <% } %>
      </div>
    </div>
  </div>
</div>

</script>

<script type="text/template" id="asuntos_edit_mini_panel_template">
<div class="panel pb0 mb0">
  <div class="panel-body">
    <div class="oh m-b">
      <h4 class="h4 pull-left">Nuevo asunto</h4>
      <i class="pull-right fa fa-times text-muted cp cerrar"></i>
    </div>
    <div class="form-group">
      <input placeholder="Nombre" type="text" name="nombre" class="form-control tab" id="asuntos_mini_nombre" value="<%= nombre %>"/>
    </div>
    <div class="form-group clearfix mb0">
      <a target="_blank" href="app/#asuntos" class="fl btn btn-default"><i class="fa fa-pencil"></i></a>
      <button class="btn guardar fr tab btn-success">Guardar</button>
    </div>
  </div>
</div>
</script>

<script type="text/template" id="consultas_item_template">
  <a class="avatar-letra avatar mt0 thumb pull-left m-r" href="app/#cliente_acciones/<%= id_contacto %>">
    <%= (isEmpty(nombre)) ? email.substr(0,1) : nombre.substr(0,1) %>
  </a>
  <div class="pull-right text-sm text-muted text-right">
    <span class="hidden-xs"><%= fecha %></b> a las <b><%= hora %> hs.</b></span>
    <% if (!isEmpty(email_usuario)) { %>
      <br/><span class="consulta_hace">
        Respondido por: <span class="label bg-light m-l-sm ng-binding"><%= email_usuario %></span>
      </span>
    <% } %>   
  </div>
  <div class="clear">
    <div>
        <a class="text-md" href="app/#cliente_acciones/<%= id_contacto %>">
          <%= (isEmpty(nombre)) ? email : nombre %>
        </a>
        <% if (!isEmpty(usuario)) { %><span class="label bg-light m-l-sm ng-binding"><%= usuario %></span><% } %>
    </div>
    <a href="app/#cliente_acciones/<%= id_contacto %>" class="text-ellipsis m-t-xs"><%= (isEmpty(texto)) ? asunto : ((texto.length > 120) ? texto.substr(0,120)+"..." : texto) %></a>
  </div>
</script>

<script type="text/template" id="consulta_cambio_estado_template">
  <div class="panel panel-default">
    <div class="panel-heading clearfix">
      <span class="font-bold fl m-t-sm">Cambio de estado</span>
      <button class="fr cp cerrar btn btn-default">
        <i class="fa fa-times text-muted"></i>
      </button>
    </div>
    <div class="panel-body">
      <div class="clearfix m-b">
        <div class="form-group">
          <label class="control-label">Motivo por el cual no se realizo la venta</label>
          <select class="form-control no-model" id="consulta_cambio_estado_motivo">
            <option value="0">Precio</option>
            <% if (ID_PROYECTO != 3) { %><option value="1">Falta de stock</option><% } %>
            <option value="2">El cliente no esta mas interesado</option>
            <option value="3">Problemas con el vendedor</option>
            <option value="99">Otro</option>
          </select>
        </div>        
        <div class="form-group">
          <label class="control-label">Notas</label>
          <textarea class="form-control no-model" id="consulta_cambio_estado_notas"></textarea>
        </div>
      </div>
    </div>
    <div class="panel-footer clearfix tar">
      <button class="btn btn-success fr guardar">Guardar</button>
    </div>
  </div>  
</script>

<script type="text/template" id="consultas_tipos_tree_panel_template">
  <div class="bg-light lter b-b wrapper-md ng-scope">
    <h1 class="m-n font-thin h3"><i class="fa fa-tags icono_principal"></i>Consultas
      / <b>Estados</b>
    </h1>
  </div>
  <div class="wrapper-md pb0">
    <div class="centrado">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <b class="fs16 pt5 fl">Organizar estados</b>
          <% if (control.check("consultas") > 1) { %>
           <a class="btn btn-info pull-right btn-addon nuevo" href="javascript:void(0)"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuevo&nbsp;&nbsp;</a>
          <% } %>
        </div>
        <div class="panel-body clearfix">
          <div ui-jq="nestable" class="dd">
          <%= workspace.crear_nestable(consultas_tipos) %>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/template" id="consultas_tipos_edit_panel_template">
<div class="panel panel-default rform">
  <div class="panel-heading">
    <b><%= (id == undefined) ? "Nueva Categoria" : nombre+" ("+id+")" %></b>
    <i class="fa fa-times cerrar fr cp"></i>
  </div>
  <div class="panel-body">
    <div class="form-group">
      <label class="control-label">Nombre</label>
      <input <%= (!edicion)?"disabled":"" %> type="text" name="nombre" class="form-control" id="consultas_tipos_nombre" value="<%= nombre %>"/>
    </div>
    <% if (control.check("emails_templates")) { %>
      <div class="form-group">
        <label class="control-label">Plantilla para enviar al cambiar a este estado</label>
        <select class="w100p" name="id_email_template" id="consultas_tipos_emails_templates"></select>
      </div>
    <% } %>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Luego de (días):</label>
          <input <%= (!edicion)?"disabled":"" %> placeholder="Días" type="text" name="tiempo_proximo_estado" class="form-control" id="consultas_tipos_tiempo_proximo_estado" value="<%= tiempo_proximo_estado %>"/>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="control-label">Pasar al siguiente estado:</label>
          <select class="form-control" id="consultas_tipos_proximo_estado" name="id_proximo_estado">
            <option <%= (id_proximo_estado == -1)?"selected":"" %> value="-1">Seleccione</option>
            <% for(var i=0;i< consultas_tipos.length;i++) { %>
              <% var tt = consultas_tipos[i] %>
              <option <%= (id_proximo_estado == tt.id)?"selected":"" %> value="<%= tt.id %>"><%= tt.nombre %></option>
            <% } %>
          </select>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label">Color</label>
      <div class="clearfix">
        <div data-selected="azul" class="opcion-color <%= (color=="azul")?"active":"" %> bg-azul"></div>
        <div data-selected="warning" class="opcion-color <%= (color=="warning")?"active":"" %> bg-warning"></div>
        <div data-selected="success" class="opcion-color <%= (color=="success")?"active":"" %> bg-success"></div>
        <div data-selected="info" class="opcion-color <%= (color=="info")?"active":"" %> bg-info"></div>
        <div data-selected="danger" class="opcion-color <%= (color=="danger")?"active":"" %> bg-danger"></div>
        <div data-selected="primary" class="opcion-color <%= (color=="primary")?"active":"" %> bg-primary"></div>
        <div data-selected="naranja" class="opcion-color <%= (color=="naranja")?"active":"" %> bg-naranja"></div>
        <div data-selected="rosa" class="opcion-color <%= (color=="rosa")?"active":"" %> bg-rosa"></div>
      </div>
    </div>
    <div class="form-group cb mb0">
      <label class="i-checks">
        <input type="checkbox" name="activo" class="checkbox" value="1" <%= (activo == 1)?"checked":"" %> <%= (!edicion)?"disabled":"" %>>
        <i></i>
        El estado esta activo.
      </label>
    </div>
  </div>
  <% if (control.check("consultas")>1) { %>
    <div class="panel-footer clearfix tar" style="border-top: none">
      <% if (id != undefined && control.check("consultas")>2) { %>
        <button class="btn btn-danger eliminar fl">Eliminar</button>
      <% } %>
      <button class="btn guardar btn-success">Guardar</button>
    </div>
  <% } %>
</div>
</script>