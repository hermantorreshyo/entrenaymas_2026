<script type="text/template" id="chat_configuracion_edit_panel_template">
<div class="bg-light lter b-b wrapper-md ng-scope">
	<h1 class="m-n font-thin h3"><i class="fa fa-cog icono_principal"></i>Configuraci&oacute;n
		/ <b>Chat</b>
	</h1>
</div>
<div class="wrapper-md">
	<div class="centrado rform">
		<div class="row">

			<div class="col-md-4">
				<div class="detalle_texto">
					<?php 
					$clave = "Configuracion / Chat / Texto 1";
					echo lang(array(
						"es"=>(isset($videos[$clave]["nombre_es"]) ? $videos[$clave]["nombre_es"] : "" ),
						"en"=>(isset($videos[$clave]["nombre_en"]) ? $videos[$clave]["nombre_en"] : "" ),
					)); ?>
				</div>
				<div class="detalle_texto_info text-muted">
					<?php echo lang(array(
						"es"=>(isset($videos[$clave]["texto_es"]) ? $videos[$clave]["texto_es"] : "" ),
						"en"=>(isset($videos[$clave]["texto_en"]) ? $videos[$clave]["texto_en"] : "" ),
					)); ?>
				</div>
				<?php if (isset($videos[$clave]["video_es"]) && !empty($videos[$clave]["video_es"])) { ?>
					<a onclick="workspace.open_video(this)" data-iframe='<?php echo $videos[$clave]["video_es"] ?>'>
						Ver video
					</a>
				<?php } ?>
			</div>

			<div class="col-md-8">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="padder">
              <div class="form-group">
                <label class="control-label">Nombre de asistente</label>
                <input type="text" class="form-control" value="<%= chat_nombre %>" name="chat_nombre" />
              </div>
              <div class="form-group">
                <label class="control-label">Pregunta especial</label>
                <textarea class="form-control" name="chat_pregunta"><%= chat_pregunta %></textarea>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">Color</label>
                    <div class="input-group color colorpicker-component">
                      <input type="text" class="form-control" value="<%= chat_color %>" />
                      <span class="input-group-addon"><i></i></span>
                    </div>
                  </div>
                </div>
              </div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="line b-b m-b-lg"></div>

		<div class="row">
			<div class="col-md-4"></div>
			<div class="col-md-8 tar">
				<button class="btn guardar btn-success">Guardar</button>
			</div>
		</div>
	</div>
</div>
</script>
