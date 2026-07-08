<script type="text/template" id="web_elegir_template_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-laptop icono_principal"></i> Elige un dise&ntilde;o para tu web</h1>
</div>
<div class="wrapper-md ng-scope tac">
  <h1 class="m-n font-thin h3" style="margin:20px 0px !important">
  	Dise&ntilde;o actual: 
  	<span class="bold" id="web_elegir_template_nombre"><%= seleccionado.nombre %></span>
  </h1>
  <div>
	<img src="<%= seleccionado.preview %>" class="mt20 mb20" id="web_elegir_template_imagen"/>
	<div class="row">
	  <div class="col-md-6 tar">
			<a href="<%= seleccionado.link_demo %>" target="_blank" class="btn w300 btn-info">Ver demo</a>
	  </div>
	  <div class="col-md-6 tal">
			<a href="app/#web_configuracion" class="btn w300 btn-success">Configurar</a>
	  </div>
	</div>
  </div>
</div>
<div class="bg-light lter b-b wrapper-md ng-scope m-t-lg">
  <h1 class="m-n font-thin h3">Cat&aacute;logo de dise&ntilde;os</h1>
</div>
<div class="wrapper-md">
	<div class="row oh m-b-lg">
		<% for(var i=0;i<opciones.length;i++) { %>
			<% var opcion = opciones[i]; %>
			<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
				<div class="template-item">
					<img src="<%= opcion.thumbnail %>"/>
					<div class="template-item-footer">
						<span class="bold white"><%= opcion.nombre %></span>
					</div>
					<div class="template-item-over">
						<div class="template-item-over-nombre"><%= opcion.nombre %></div>
						<div class="btn-item">
							<button class="btn">Ver demo</button>
						</div>
						<div class="btn-item">
							<button data-id="<%= opcion.id %>" class="btn elegir_disenio">Elegir dise&ntilde;o</button>
						</div>
					</div>	  
				</div>
			</div>
		<% } %>
	</div>
	<div class="row text-center">
	  <div class="col-xs-12 col-sm-4">
		<div class="panel padder padder-v dashboard_data_item">
		  <div class="media">
			  <img class="thumb-lg pull-left" src="/sistema/resources/images/elegir-template-1.png"/>
			  <span class="info">Todos los dise&ntilde;os se adaptan perfectamente al rubro de tu negocio con simples pasos de personalizaci&oacute;n.</span>
		  </div>
		</div>
	  </div>
	  <div class="col-xs-12 col-sm-4">
		<div class="panel padder padder-v dashboard_data_item">
		  <div class="media">
			  <img class="thumb-lg pull-left" src="/sistema/resources/images/elegir-template-2.png"/>
			  <span class="info">Las im&aacute;genes y colores de cada dise&ntilde;o son referenciales, vas a poder cambiarlos al momento de personalizarlos.</span>
		  </div>
		</div>
	  </div>
	  <div class="col-xs-12 col-sm-4">
		<div class="panel padder padder-v dashboard_data_item">
		  <div class="media">
			  <img class="thumb-lg pull-left" src="/sistema/resources/images/elegir-template-3.png"/>
			  <span class="info">Si necesitas ayuda, estaremos disponibles para ayudarte y asegurarte que tu sitio siempre se vea bien!</span>
		  </div>
		</div>
	  </div>  
	</div>
	<div class="m-b oh">
		<table class="w100p">
			<tr>
				<td class="bg-info wrapper" style="width:75%">
					<img src="/sistema/resources/images/edit3.png" class="mr10 fl"/>
					<span class="bold">No encontraste lo que buscabas?</span>
					Somos especialistas haciendo dise&ntilde;os a medida para lograr una imagen &uacute;nica y profesional de tu negocio.
				</td>
				<td class="bg-success wrapper tac">
					<a>CONTRATAR DISE&Ntilde;O A MEDIDA</a>			
				</td>
			</tr>
		</table>
	</div>
</div>
</script>