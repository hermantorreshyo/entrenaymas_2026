<script type="text/template" id="empresa_restovar_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n / 
  	<b>Mi Empresa</b>
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
		  					<label class="control-label">Nombre</label>
		  					<input type="text" name="nombre" class="form-control" id="empresas_detalle_nombre" value="<%= nombre %>"/>
		  				</div>
							<div class="form-group">
								<label class="control-label">Descripci&oacute;n</label>
								<div class="form-group">
									<textarea name="texto_comercio" name="texto_comercio" id="empresas_detalle_texto_comercio"><%= texto_comercio %></textarea>
								</div>
							</div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tel&eacute;fono</label>
                    <input type="text" name="telefono" class="form-control" id="empresas_detalle_telefono" value="<%= telefono %>"/>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Email</label>
                    <input type="text" name="email" class="form-control" id="empresas_detalle_email" value="<%= email %>"/>
                  </div>
                </div>
              </div>
						</div>
					</div>
				</div>

        <?php /*
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">
							<div class="form-group">
								<label class="control-label">
									<?php echo lang(array(
										"es"=>"Rubros",
									)); ?>
								</label>
								<input class="select2_textarea w100p" type="text" id="empresas_detalle_categorias"/>
							</div>
						</div>
					</div>
				</div>
        */ ?>

				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">
							<div class="form-group mb0 clearfix">
								<label class="control-label">Logos e im&aacute;genes</label>
								<a id="expand_mapa" class="expand-link fr">
									<?php echo lang(array(
										"es"=>"+ Ver opciones",
										"en"=>"+ View options",
									)); ?>
								</a>
								<div class="panel-description">
									Suba el logo de su empresa.
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body expand">
						<div class="padder">
			  			<% if (id != undefined) { %>
	  						<div class="form-group">
								  <?php
								  single_upload(array(
									"name"=>"logo",
									"label"=>"Logo Horizontal",
									"url"=>"empresas/function/save_image/",
									"resizable"=>1,
									"description"=>"Utilizado en los comprobantes, remitos, presupuestos, etc. Tama&ntilde;o recomendado: 450 x 280 p&iacute;xeles"
								  )); ?>
								</div>
	  						<div class="form-group">
								  <?php
								  single_upload(array(
									"name"=>"path",
									"label"=>"Logo Cuadrado",
									"url"=>"empresas/function/save_image/",
									"width"=>400,
									"height"=>400,
									"description"=>"Utilizado como imagen de perfil del sistema. Tama&ntilde;o recomendado: 200 x 200 p&iacute;xeles"
								  )); ?>
			  				</div>
			  			<% } %>
			  		</div>
			  	</div>
			  </div>

				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">
							<div class="form-group mb0 clearfix">
								<label class="control-label">Datos impositivos</label>
								<a id="expand_mapa" class="expand-link fr">
									<?php echo lang(array(
										"es"=>"+ Ver opciones",
										"en"=>"+ View options",
									)); ?>
								</a>
								<div class="panel-description">
									Ingrese su raz&oacute;n social, CUIT, tipo de IVA, etc.
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body expand">
						<div class="padder">
	  					<div class="form-group">
	  						<label class="control-label">Raz&oacute;n Social</label>
	  						<input type="text" name="razon_social" class="form-control" id="empresas_detalle_razon_social" value="<%= razon_social %>"/>
	  					</div>
			  			<div class="row">
			  				<div class="col-md-6">
			  					<div class="form-group">
			  						<label class="control-label">Tipo de contribuyente</label>
										<select class="form-control" name="tipo_contribuyente" id="empresas_detalle_tipo_contribuyente">
											<option value="2" <%= (id_tipo_contribuyente == 2) ? "selected": "" %>>Monotributo</option>
											<option value="1" <%= (id_tipo_contribuyente == 1) ? "selected": "" %>>Responsable Inscripto</option>
											<option value="3" <%= (id_tipo_contribuyente == 3) ? "selected": "" %>>Exento</option>
										</select>
									</div>
			  				</div>
			  				<div class="col-md-6">
			  					<div class="form-group">
			  						<label class="control-label">CUIT</label>
			  						<input type="text" name="cuit" class="form-control" id="empresas_detalle_cuit" value="<%= cuit %>"/>
			  					</div>
			  				</div>
			  			</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" style="<%= (ID_EMPRESA == 571)?"display:none":"" %>">
					<div class="panel-body">
						<div class="padder">
							<div class="form-group mb0 clearfix">
								<label class="control-label">Ubicaci&oacute;n</label>
								<a id="expand_mapa" class="expand-link fr">
									<?php echo lang(array(
										"es"=>"+ Ver opciones",
										"en"=>"+ View options",
									)); ?>
								</a>
								<div class="panel-description">
									Indique la ubicaci&oacute;n exacta y el rango de alcance de su comercio.
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body expand">
						<div class="padder">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Direcci&oacute;n</label>
										<input type="text" id="empresas_detalle_direccion" value="<%= direccion %>" class="form-control" name="direccion" value="<%= direccion %>" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Localidad</label>
										<div class="input-group">
											<input type="text" name="localidad" id="empresas_detalle_localidad" value="<%= localidad %>" class="form-control"/>
											<span class="input-group-btn">
												<button class="btn btn-info add_marker">
													<?php echo lang(array(
														"es"=>"+ Marcador",
														"en"=>"+ Marker",
													)); ?>
												</button>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div style="height:400px;" id="empresa_detalle_mapa"></div>
							<div class="help-block">Puede arrastrar el marcador del mapa para ponerlo en la direccion exacta. Doble click para eliminarlo. </div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Alcance de entrega (km)</label>
										<input type="text" id="empresas_radio" value="<%= radio %>" class="form-control" name="direccion" value="<%= direccion %>" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" style="<%= (ID_EMPRESA == 571)?"display:none":"" %>">
					<div class="panel-body">
						<div class="padder">
							<div class="form-group mb0 clearfix">
								<label class="control-label">Horarios de atenci&oacute;n</label>
								<a id="expand_mapa" class="expand-link fr">
									<?php echo lang(array(
										"es"=>"+ Ver opciones",
										"en"=>"+ View options",
									)); ?>
								</a>
								<div class="panel-description">
									Dias y horarios en los que su comercio esta abierto al publico.
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body expand">
						<div class="padder">
							<div class="form-group">
							  <div class="form-inline">
								  <label class="control-label tal col-lg-2 col-md-3 col-sm-4">Lunes</label>
								  <input type="text" id="empresas_detalle_horario_lunes_1" name="horario_lunes_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_lunes_1 %>"/>
								  <input type="text" id="empresas_detalle_horario_lunes_2" name="horario_lunes_2" placeholder="Ej: 13:00" class="form-control w100" value="<%= horario_lunes_2 %>"/>
								  <span class="m-l m-r">-</span>
								  <input type="text" id="empresas_detalle_horario_lunes_3" name="horario_lunes_3" placeholder="Ej: 18:00" class="form-control w100" value="<%= horario_lunes_3 %>"/>
								  <input type="text" id="empresas_detalle_horario_lunes_4" name="horario_lunes_4" placeholder="Ej: 24:00" class="form-control w100" value="<%= horario_lunes_4 %>"/>
							  </div>
							</div>
							<div class="form-group">
							  <div class="form-inline">
								  <label class="control-label tal col-lg-2 col-md-3 col-sm-4">Martes</label>
								  <input type="text" id="empresas_detalle_horario_martes_1" name="horario_martes_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_martes_1 %>"/>
								  <input type="text" id="empresas_detalle_horario_martes_2" name="horario_martes_2" placeholder="Ej: 13:00" class="form-control w100" value="<%= horario_martes_2 %>"/>
								  <span class="m-l m-r">-</span>
								  <input type="text" id="empresas_detalle_horario_martes_3" name="horario_martes_3" placeholder="Ej: 18:00" class="form-control w100" value="<%= horario_martes_3 %>"/>
								  <input type="text" id="empresas_detalle_horario_martes_4" name="horario_martes_4" placeholder="Ej: 24:00" class="form-control w100" value="<%= horario_martes_4 %>"/>
							  </div>
							</div>
							<div class="form-group">
							  <div class="form-inline">
								  <label class="control-label tal col-lg-2 col-md-3 col-sm-4">Miercoles</label>
								  <input type="text" id="empresas_detalle_horario_miercoles_1" name="horario_miercoles_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_miercoles_1 %>"/>
								  <input type="text" id="empresas_detalle_horario_miercoles_2" name="horario_miercoles_2" placeholder="Ej: 13:00" class="form-control w100" value="<%= horario_miercoles_2 %>"/>
								  <span class="m-l m-r">-</span>
								  <input type="text" id="empresas_detalle_horario_miercoles_3" name="horario_miercoles_3" placeholder="Ej: 18:00" class="form-control w100" value="<%= horario_miercoles_3 %>"/>
								  <input type="text" id="empresas_detalle_horario_miercoles_4" name="horario_miercoles_4" placeholder="Ej: 24:00" class="form-control w100" value="<%= horario_miercoles_4 %>"/>
							  </div>
							</div>
							<div class="form-group">
							  <div class="form-inline">
								  <label class="control-label tal col-lg-2 col-md-3 col-sm-4">Jueves</label>
								  <input type="text" id="empresas_detalle_horario_jueves_1" name="horario_jueves_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_jueves_1 %>"/>
								  <input type="text" id="empresas_detalle_horario_jueves_2" name="horario_jueves_2" placeholder="Ej: 13:00" class="form-control w100" value="<%= horario_jueves_2 %>"/>
								  <span class="m-l m-r">-</span>
								  <input type="text" id="empresas_detalle_horario_jueves_3" name="horario_jueves_3" placeholder="Ej: 18:00" class="form-control w100" value="<%= horario_jueves_3 %>"/>
								  <input type="text" id="empresas_detalle_horario_jueves_4" name="horario_jueves_4" placeholder="Ej: 24:00" class="form-control w100" value="<%= horario_jueves_4 %>"/>
							  </div>
							</div>
							<div class="form-group">
							  <div class="form-inline">
								  <label class="control-label tal col-lg-2 col-md-3 col-sm-4">Viernes</label>
								  <input type="text" id="empresas_detalle_horario_viernes_1" name="horario_viernes_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_viernes_1 %>"/>
								  <input type="text" id="empresas_detalle_horario_viernes_2" name="horario_viernes_2" placeholder="Ej: 13:00" class="form-control w100" value="<%= horario_viernes_2 %>"/>
								  <span class="m-l m-r">-</span>
								  <input type="text" id="empresas_detalle_horario_viernes_3" name="horario_viernes_3" placeholder="Ej: 18:00" class="form-control w100" value="<%= horario_viernes_3 %>"/>
								  <input type="text" id="empresas_detalle_horario_viernes_4" name="horario_viernes_4" placeholder="Ej: 24:00" class="form-control w100" value="<%= horario_viernes_4 %>"/>
							  </div>
							</div>
							<div class="form-group">
							  <div class="form-inline">
								  <label class="control-label tal col-lg-2 col-md-3 col-sm-4">Sabado</label>
								  <input type="text" id="empresas_detalle_horario_sabado_1" name="horario_sabado_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_sabado_1 %>"/>
								  <input type="text" id="empresas_detalle_horario_sabado_2" name="horario_sabado_2" placeholder="Ej: 13:00" class="form-control w100" value="<%= horario_sabado_2 %>"/>
								  <span class="m-l m-r">-</span>
								  <input type="text" id="empresas_detalle_horario_sabado_3" name="horario_sabado_3" placeholder="Ej: 18:00" class="form-control w100" value="<%= horario_sabado_3 %>"/>
								  <input type="text" id="empresas_detalle_horario_sabado_4" name="horario_sabado_4" placeholder="Ej: 24:00" class="form-control w100" value="<%= horario_sabado_4 %>"/>						  
							  </div>
							</div>
							<div class="form-group">
							  <div class="form-inline">
								  <label class="control-label tal col-lg-2 col-md-3 col-sm-4">Domingo</label>
								  <input type="text" id="empresas_detalle_horario_domingo_1" name="horario_domingo_1" placeholder="Ej: 09:00" class="form-control w100" value="<%= horario_domingo_1 %>"/>
								  <input type="text" id="empresas_detalle_horario_domingo_2" name="horario_domingo_2" placeholder="Ej: 13:00" class="form-control w100" value="<%= horario_domingo_2 %>"/>
								  <span class="m-l m-r">-</span>
								  <input type="text" id="empresas_detalle_horario_domingo_3" name="horario_domingo_3" placeholder="Ej: 18:00" class="form-control w100" value="<%= horario_domingo_3 %>"/>
								  <input type="text" id="empresas_detalle_horario_domingo_4" name="horario_domingo_4" placeholder="Ej: 24:00" class="form-control w100" value="<%= horario_domingo_4 %>"/>						  
							  </div>
							</div>
							<div class="form-group">
								<div class="checkbox">
									<label class="i-checks">
										<input type="checkbox" id="empresas_acepta_pedidos_fuera_horario" name="acepta_pedidos_fuera_horario" class="checkbox" value="1" <%= (acepta_pedidos_fuera_horario == 1)?"checked":"" %> >
										<i></i>
										Aceptar pedidos fuera de horario
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default" style="<%= (ID_EMPRESA == 571)?"display:none":"" %>">
					<div class="panel-body">
						<div class="padder">
							<div class="form-group mb0 clearfix">
								<label class="control-label">Costos de env&iacute;o</label>
								<a id="expand_mapa" class="expand-link fr">
									<?php echo lang(array(
										"es"=>"+ Ver opciones",
										"en"=>"+ View options",
									)); ?>
								</a>
								<div class="panel-description">
									Agregue un costo por el env&iacute;o de sus productos.
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body expand">
						<div class="padder">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Costo de env&iacute;o</label>
										<input type="text" name="costo_envio" id="empresas_detalle_costo_envio" value="<%= costo_envio %>" class="form-control"/>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Pedido m&iacute;nimo</label>
										<input type="text" name="pedido_minimo" id="empresas_detalle_pedido_minimo" value="<%= pedido_minimo %>" class="form-control"/>
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
								<label class="control-label">Notificaciones</label>
								<a id="expand_mapa" class="expand-link fr">
									<?php echo lang(array(
										"es"=>"+ Ver opciones",
										"en"=>"+ View options",
									)); ?>
								</a>
								<div class="panel-description">
									Datos utilizados para recibir los pedidos y las notificaciones del sistema.
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body expand">
						<div class="padder">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Tel&eacute;fono</label>
										<input type="text" name="telefono_delivery" id="empresas_detalle_telefono_delivery" value="<%= telefono_delivery %>" class="form-control"/>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">Email</label>
										<input type="text" name="email_delivery" id="empresas_detalle_email_delivery" value="<%= email_delivery %>" class="form-control"/>
									</div>
								</div>
							</div>
							<div class="form-group" style="<%= (ID_EMPRESA == 571)?"display:none":"" %>">
								<div class="checkbox">
									<label class="i-checks">
										<input type="checkbox" id="empresas_detalle_usar_sms" name="usar_sms" class="checkbox" value="1" <%= (usar_sms == 1)?"checked":"" %> >
										<i></i>
										Utilizar SMS para notificar pedidos, eventos, etc.
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="row">
			<div class="col-md-10 col-md-offset-1 tar">
        <div class="line b-b m-b-lg"></div>
				<button class="btn guardar btn-success">Guardar</button>
			</div>
		</div>

	</div>
</div>
</script>