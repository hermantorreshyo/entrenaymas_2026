<script type="text/template" id="web_configuracion_email_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
  <h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
  	/ <b>Emails</b>
  </h1>
</div>
<div class="wrapper-md">
    <div class="centrado rform">
        <div class="row">
            <div class="col-md-4">
                <div class="detalle_texto">Env&iacute;o de emails</div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                    
                        <div class="padder">
				  			
		  					<div class="form-group">
			  					<label class="control-label">Alquileres</label>
			  					<input type="text" name="emails_alquileres" class="form-control" id="empresas_emails_alquileres" value="<%= emails_alquileres %>"/>
			  				</div>
		  					<div class="form-group">
			  					<label class="control-label">Ventas</label>
			  					<input type="text" name="emails_ventas" class="form-control" id="empresas_emails_ventas" value="<%= emails_ventas %>"/>
			  				</div>
		  					<div class="form-group">
			  					<label class="control-label">Emprendimientos</label>
			  					<input type="text" name="emails_emprendimientos" class="form-control" id="empresas_emails_emprendimientos" value="<%= emails_emprendimientos %>"/>
			  				</div>
		  					<div class="form-group">
			  					<label class="control-label">Tasaciones</label>
			  					<input type="text" name="emails_tasaciones" class="form-control" id="empresas_emails_tasaciones" value="<%= emails_tasaciones %>"/>
			  				</div>
		  					<div class="form-group">
			  					<label class="control-label">Contacto</label>
			  					<input type="text" name="emails_contacto" class="form-control" id="empresas_emails_contacto" value="<%= emails_contacto %>"/>
			  				</div>
		  					<div class="form-group">
			  					<label class="control-label">Registro</label>
			  					<input type="text" name="emails_registro" class="form-control" id="empresas_emails_registro" value="<%= emails_registro %>"/>
			  				</div>
				  			
				  		</div>
				  	</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-8">
				<button class="btn btn-success guardar">Guardar</button>
			</div>
		</div>

	</div>
</div>
</script>
